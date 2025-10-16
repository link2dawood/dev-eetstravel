# Tabler Theme Implementation Guide

## Overview
This document provides a comprehensive guide for implementing the Tabler theme with sidebar across all modules in the TMS (Tour Management System) application.

## ✅ Completed Tasks

### 1. Theme Assets Installation
- **Location**: `/var/www/html/public/tabler/`
- **Files Downloaded**:
  - `css/tabler.min.css` - Core Tabler CSS
  - `js/tabler.min.js` - Core Tabler JavaScript
  - `css/tabler-icons.min.css` - Tabler Icons font

### 2. Core Layout Files Created

#### Main Layout
- **File**: `/var/www/html/resources/views/scaffold-interface/layouts/tabler-app.blade.php`
- **Purpose**: Main application layout replacing the old AdminLTE layout
- **Features**:
  - Responsive sidebar navigation
  - Modern header with notifications, messages, and tasks
  - Footer component
  - All existing JavaScript and CSS dependencies preserved

#### Sidebar Navigation
- **File**: `/var/www/html/resources/views/scaffold-interface/layouts/tabler-sidebar.blade.php`
- **Features**:
  - Vertical collapsible sidebar
  - Dropdown menus for module groups
  - Tabler icons (ti ti-*) throughout
  - All permission checks preserved
  - Active state highlighting

#### Header Component
- **File**: `/var/www/html/resources/views/scaffold-interface/layouts/tabler-header.blade.php`
- **Features**:
  - Notifications dropdown
  - Messages counter with link to email
  - Tasks dropdown with counts
  - User profile menu with avatar
  - Logout functionality

#### Footer Component
- **File**: `/var/www/html/resources/views/scaffold-interface/layouts/tabler-footer.blade.php`
- **Features**:
  - Copyright information
  - Links to documentation and support

### 3. Reusable Components

#### Page Header Component
- **File**: `/var/www/html/resources/views/components/tabler-page-header.blade.php`
- **Usage**:
```blade
<x-tabler-page-header
    :title="'Page Title'"
    :breadcrumbs="[
        ['title' => 'Home', 'icon' => 'home', 'route' => url('/home')],
        ['title' => 'Current Page', 'route' => null]
    ]"
    :actions="$actionButtonsHtml"
/>
```

### 4. Example Modules Updated

#### Hotels Module (✅ Complete)
- **File**: `/var/www/html/resources/views/hotel/index.blade.php`
- **Changes**:
  - Extended `tabler-app` layout
  - New page header with breadcrumbs
  - Card-based table design
  - Tabler icons replacing FontAwesome
  - Search and export functionality preserved

#### Clients Module (✅ Complete)
- **File**: `/var/www/html/resources/views/clients/index.blade.php`
- **Changes**: Same as Hotels module

---

## 📋 Remaining Tasks

### Modules to Update

The following modules still need to be converted from AdminLTE to Tabler theme:

#### 🔴 High Priority
1. **Tour Module** (`/resources/views/tour/`)
   - index.blade.php (complex with tabs)
   - create.blade.php
   - edit.blade.php
   - show.blade.php

2. **Task Module** (`/resources/views/task/`)
   - index.blade.php
   - create.blade.php
   - edit.blade.php
   - show.blade.php

3. **User Administration** (`/resources/views/scaffold-interface/`)
   - users/index.blade.php
   - roles/index.blade.php
   - permissions/index.blade.php

#### 🟡 Medium Priority
4. **Services Modules**
   - events/ (all files)
   - guide/ (all files)
   - restaurant/ (all files)
   - driver/ (all files)

5. **Bus Company**
   - bus/ (all files)
   - transfer/ (all files)

6. **Base Input**
   - status/ (all files)
   - room_types/ (all files)
   - rate/ (all files)
   - currency_rate/ (all files)
   - currencies/ (all files)
   - criteria/ (all files)

7. **Communications**
   - templates/ (all files)
   - email/ (all files)
   - comments/ (all files)

#### 🟢 Lower Priority
8. **Accounting**
   - accounting/ (all files)
   - invoices/ (all files)
   - office/ (all files)
   - reporting/ (all files)
   - taxes/ (all files)

---

## 🔄 Conversion Pattern

### Step 1: Update Layout Extension
**OLD:**
```blade
@extends('scaffold-interface.layouts.app')
```

**NEW:**
```blade
@extends('scaffold-interface.layouts.tabler-app')
```

### Step 2: Replace Breadcrumb Section
**OLD:**
```blade
@include('layouts.title',
   ['title' => 'Module Name', 'sub_title' => 'Module List',
   'breadcrumbs' => [
   ['title' => 'Home', 'icon' => 'dashboard', 'route' => url('/home')],
   ['title' => 'Module', 'icon' => 'icon-name', 'route' => null]]])
```

**NEW:**
```blade
<!-- Page header -->
<div class="page-header d-print-none">
    <div class="container-xl">
        <div class="row g-2 align-items-center">
            <div class="col">
                <div class="page-pretitle">
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item"><a href="{{ url('/home') }}"><i class="ti ti-home"></i> Home</a></li>
                            <li class="breadcrumb-item active" aria-current="page">Module Name</li>
                        </ol>
                    </nav>
                </div>
                <h2 class="page-title">Module Name</h2>
            </div>
            <div class="col-auto ms-auto d-print-none">
                <div class="btn-list">
                    {!! \App\Helper\PermissionHelper::getCreateButton(route('module.create'), \App\Module::class) !!}
                </div>
            </div>
        </div>
    </div>
</div>
```

### Step 3: Convert Content Structure
**OLD:**
```blade
<section class="content">
    <div class="box box-primary">
        <div class="box-body">
            <!-- content -->
        </div>
    </div>
</section>
```

**NEW:**
```blade
<!-- Page body -->
<div class="page-body">
    <div class="container-xl">
        <div class="row row-deck row-cards">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Module List</h3>
                    </div>
                    <div class="card-body">
                        <!-- content -->
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
```

### Step 4: Update Table Classes
**OLD:**
```blade
<table class="table table-striped table-bordered table-hover bootstrap-table">
```

**NEW:**
```blade
<table class="table card-table table-vcenter text-nowrap datatable">
```

### Step 5: Replace Icons
**OLD:**
```html
<i class="fa fa-home"></i>
<i class="fa fa-user"></i>
<i class="fa fa-envelope"></i>
```

**NEW:**
```html
<i class="ti ti-home"></i>
<i class="ti ti-user"></i>
<i class="ti ti-mail"></i>
```

### Common Icon Mappings:
| FontAwesome | Tabler Icons |
|-------------|--------------|
| fa-dashboard | ti-dashboard |
| fa-home | ti-home |
| fa-user | ti-user |
| fa-users | ti-users |
| fa-envelope | ti-mail |
| fa-tasks | ti-checkbox |
| fa-hotel | ti-building |
| fa-suitcase | ti-briefcase |
| fa-calendar | ti-calendar |
| fa-download | ti-download |
| fa-upload | ti-upload |
| fa-edit | ti-edit |
| fa-trash | ti-trash |
| fa-eye | ti-eye |
| fa-plus | ti-plus |
| fa-cog | ti-settings |

### Step 6: Update Alert/Message Boxes
**OLD:**
```blade
<div class="alert alert-info col-md-12">
```

**NEW:**
```blade
<div class="alert alert-info m-3">
```

### Step 7: Preserve All Logic
**IMPORTANT**: Keep all these unchanged:
- PHP logic (`@if`, `@foreach`, `@forelse`, etc.)
- Route names and URLs
- Form fields and validation
- JavaScript sections
- Permission checks
- Data attributes

---

## 🎨 CSS Class Reference

### Layout Classes
| Old (AdminLTE) | New (Tabler) |
|----------------|--------------|
| `box` | `card` |
| `box-primary` | `card` |
| `box-header` | `card-header` |
| `box-body` | `card-body` |
| `box-footer` | `card-footer` |
| `content` | `page-body` |

### Button Classes (Mostly Same)
| Old | New |
|-----|-----|
| `btn btn-primary` | `btn btn-primary` ✓ |
| `btn btn-success` | `btn btn-success` ✓ |
| `btn btn-danger` | `btn btn-danger` ✓ |
| `pull-right` | `ms-auto` |

### Table Classes
| Old | New |
|-----|-----|
| `table-striped` | (built into card-table) |
| `table-bordered` | (built into card-table) |
| `table-hover` | (built into card-table) |
| Use: `table card-table table-vcenter text-nowrap datatable` |

---

## 🧪 Testing Checklist

After converting each module, test:

- [ ] Page loads without errors
- [ ] Sidebar navigation works
- [ ] Breadcrumbs display correctly
- [ ] Tables display and sort properly
- [ ] Search functionality works
- [ ] Create/Edit/Delete buttons work
- [ ] Modals open and function correctly
- [ ] Forms submit properly
- [ ] Pagination works
- [ ] Responsive design (mobile/tablet)
- [ ] All JavaScript functions work
- [ ] Export functions work
- [ ] Notifications display

---

## 📝 Quick Reference Commands

### Find all index files to update:
```bash
find /var/www/html/resources/views -name "index.blade.php" -type f | grep -v vendor | grep -v node_modules
```

### Find files still using old layout:
```bash
grep -r "@extends('scaffold-interface.layouts.app')" /var/www/html/resources/views --include="*.blade.php"
```

### Count remaining files:
```bash
grep -r "@extends('scaffold-interface.layouts.app')" /var/www/html/resources/views --include="*.blade.php" | wc -l
```

---

## 🚀 Next Steps

1. **Start with high-priority modules**: Tour, Task, User Administration
2. **Use the Hotels/Clients modules as reference**
3. **Test each module after conversion**
4. **Update forms (create.blade.php, edit.blade.php)** with Tabler form classes
5. **Update show pages** with Tabler card designs
6. **Create custom CSS** in `/public/tabler/css/custom.css` if needed

---

## 💡 Tips

1. **Work module by module** - Complete all files in one module before moving to the next
2. **Keep a backup** - Git commit after each module conversion
3. **Test thoroughly** - Don't just check if it displays, test all functionality
4. **Preserve custom JavaScript** - Keep all existing JS functions intact
5. **Mobile first** - Test on mobile devices or use browser dev tools

---

## 📚 Resources

- **Tabler Documentation**: https://preview.tabler.io/
- **Tabler Icons**: https://tabler-icons.io/
- **GitHub Repository**: https://github.com/tabler/tabler
- **Layout Examples**: Check `/var/www/html/resources/views/hotel/index.blade.php` and `/var/www/html/resources/views/clients/index.blade.php`

---

## ⚠️ Important Notes

- **DO NOT modify controllers** - Only update view files
- **Keep all routes the same** - Don't change route names or URLs
- **Preserve all PHP logic** - Only change HTML/CSS structure
- **Test permissions** - Ensure permission checks still work
- **Backup database** - Before testing extensively

---

## 📊 Progress Tracker

Track your progress here:

### Core Setup
- [x] Tabler assets downloaded
- [x] Main layout created
- [x] Sidebar created
- [x] Header created
- [x] Footer created
- [x] Reusable components created

### Modules Converted
- [x] Hotels
- [x] Clients
- [ ] Tours
- [ ] Tasks
- [ ] Events
- [ ] Guides
- [ ] Restaurants
- [ ] Drivers
- [ ] Buses
- [ ] Transfers
- [ ] Status
- [ ] Room Types
- [ ] Rates
- [ ] Currency Rates
- [ ] Currencies
- [ ] Criteria
- [ ] Users
- [ ] Roles
- [ ] Permissions
- [ ] Templates
- [ ] Email
- [ ] Comments
- [ ] Accounting
- [ ] Invoices
- [ ] Office
- [ ] Reporting
- [ ] Taxes

---

**Last Updated**: {{date('Y-m-d H:i:s')}}
**Version**: 1.0
