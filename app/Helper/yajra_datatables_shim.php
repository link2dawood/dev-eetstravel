<?php
/**
 * Back-compat shim for the old yajra/laravel-datatables namespace.
 *
 * Pre-v9 of yajra/laravel-datatables-oracle exposed the main class as
 * `Yajra\Datatables\Datatables` (lower-case 'Datatables'). v9+ renamed
 * it to `Yajra\DataTables\DataTables` (PascalCase).
 *
 * PHP class names are case-INsensitive at the engine level, but PSR-4
 * autoloading is case-SENSITIVE on the namespace-to-path map → an
 * existing `use Yajra\Datatables\Datatables;` statement can't autoload
 * anything from the v9 package (the file is at .../src/DataTables.php
 * under namespace Yajra\DataTables\, not Yajra\Datatables\).
 *
 * The codebase has ~30 `use Yajra\Datatables\Datatables;` references
 * across the controllers. Force-loading the canonical PascalCase class
 * here is enough — once PHP knows it, the case-insensitive class-name
 * table makes every later `Yajra\Datatables\Datatables` reference
 * resolve to the same class without needing each `use` line touched.
 *
 * Wired into composer.json's `autoload.files`, so it runs after the
 * vendor autoloader is loaded and before any controller is dispatched.
 */
if (class_exists('Yajra\\DataTables\\DataTables')) {
    // The class_exists call triggers the PSR-4 autoload of DataTables.php.
    // PHP's case-insensitive class table now resolves
    // `Yajra\Datatables\Datatables` to the same class. No further action.
}
