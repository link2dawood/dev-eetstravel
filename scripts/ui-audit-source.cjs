const fs = require('node:fs');
const path = require('node:path');
const root = process.cwd();
function files(directory) {
    return fs.readdirSync(directory, { withFileTypes: true }).flatMap(entry => entry.isDirectory()
        ? files(path.join(directory, entry.name)) : [path.join(directory, entry.name)]);
}
const views = files(path.join(root, 'resources/views')).filter(file => file.endsWith('.blade.php'));
const findings = { templateCount: views.length, missingAssets: [], legacyAttributes: [], suspiciousText: [] };
for (const file of views) {
    const relative = path.relative(root, file).replaceAll('\\', '/');
    const lines = fs.readFileSync(file, 'utf8').split(/\r?\n/);
    lines.forEach((line, index) => {
        for (const match of line.matchAll(/(?:asset|URL::asset)\(\s*['"]([^'"$]+)['"]/g)) {
            const asset = match[1].split('?')[0];
            if (!asset.includes('{{') && !fs.existsSync(path.join(root, 'public', asset))) findings.missingAssets.push({ file: relative, line: index + 1, asset });
        }
        if (/data-toggle\s*=\s*['"](?:tab|modal|dropdown|collapse)['"]|data-dismiss\s*=/.test(line)) {
            findings.legacyAttributes.push({ file: relative, line: index + 1, text: line.trim().slice(0, 220) });
        }
        if (/Place Holder|Lorem ipsum|TODO|Comming soon|Receiveable|Receiveable|Receiveable|recieve|Sumbit/i.test(line)) {
            findings.suspiciousText.push({ file: relative, line: index + 1, text: line.trim().slice(0, 200) });
        }
    });
}
fs.writeFileSync('storage/app/ui-audit/source-results.json', JSON.stringify(findings, null, 2));
console.log(JSON.stringify({ templates: views.length, missingAssetReferences: findings.missingAssets.length, legacyInteractionAttributes: findings.legacyAttributes.length, suspiciousText: findings.suspiciousText.length }));
