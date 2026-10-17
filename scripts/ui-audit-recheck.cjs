const fs=require('node:fs');
const {request}=require(process.env.PLAYWRIGHT_MODULE||'playwright');
(async()=>{
    const state=JSON.parse(fs.readFileSync('storage/app/sample-data/section-checks/browser-state.json'));
    state.cookies=state.cookies.map(cookie=>({...cookie,expires:cookie.expires??-1}));
    const context=await request.newContext({storageState:state});
    const results=[];
    try{
        for(const path of ['/transfer/5/edit','/tour/3','/holiday/1']){
            const start=Date.now();
            try{
                const response=await context.get('http://127.0.0.1:8010'+path,{timeout:60000});
                const body=await response.text();
                const file='recheck-'+path.replaceAll('/','-')+'.html';
                fs.writeFileSync('storage/app/ui-audit/'+file,body);
                results.push({path,status:response.status(),milliseconds:Date.now()-start,file});
            }catch(error){results.push({path,milliseconds:Date.now()-start,error:error.message});}
            console.log(JSON.stringify(results.at(-1)));
        }
        fs.writeFileSync('storage/app/ui-audit/recheck-results.json',JSON.stringify(results,null,2));
    }finally{await context.dispose();}
})().catch(error=>{console.error(error);process.exitCode=1;});
