# Sela Platform - Project Modifications & Architecture Ledger

This document serves as a centralized, high-density ledger of all structural, architectural, and design changes applied to the Sela Platform. It prevents unnecessary directory scans and provides immediate context for future development and agent sessions.

---

## 📅 Last Updated: May 19, 2026

---

## 1. ⚙️ Super Admin Settings Portal (Phrases & Internationalization)
* **Goal**: Modernize the settings engine, support total customization of landing page phrases, SMTP parameters, and 30+ new Mobile App phrases dynamically without breaking under PHP 8.

### Key Modifications:
* **Backend Controller Refactoring**:
  * **File**: `app/Controllers/SuperAdmin/Settings.php`
  * **Changes**:
    * Integrated a robust default-mapping system supporting 30 new mobile app phrase keys (screen titles, description strings, action buttons, verification labels).
    * Implemented auto-seeding of missing keys directly into the live production database (`102.213.180.22`) on page load.
    * Added `/superadmin/settings/repair` route which handles:
      1. Converting the settings `type` column to `VARCHAR(191)` (prevents BLOB key constraint issues).
      2. Cleaning and deduplicating key records in-memory.
      3. Applying a hard `UNIQUE` database constraint on the `type` column.
* **Frontend View Redesign**:
  * **File**: `app/Views/superadmin/settings/index.php`
  * **Changes**:
    * Structured the UI into a clean, searchable, responsive 2-column layout.
    * Removed fragile PHP 8 variable-variable evaluations (`$$key ?? ''`) which caused rendering failures.
    * Enforced safe, array-based null-coalescing values: `$all_settings[$key] ?? ''` across all loop categories (SMTP, Landing Page, Mobile App Views, Mobile Descriptions, Welcome/Login, and Action Labels).
    * Integrated a fast local JS-based search engine filtering categories and inputs dynamically.

---

## 2. 📱 Mobile App Integration & API Additions
* **Goal**: Enable the Flutter mobile application to synchronize configurations, dynamic phrases, and support advanced student management features.

### Key Modifications:
* **Dynamic Title and Phrase Synchronization**:
  * **Files**:
    * `mobile_app/lib/services/app_titles.dart`
    * `mobile_app/lib/screens/*` (All mobile screens)
  * **Changes**:
    * Configured UI text components to fetch values dynamically from the API (`/api/settings`) with local, high-fidelity Arabic fallbacks.
* **Student Photo Upload Capability**:
  * **File**: `app/Controllers/Api/MobileApi.php`
  * **Changes**:
    * Created endpoint `uploadStudentPhoto` which decodes base64-uploaded image payloads, stores them securely under `public/upload/student_images/`, and updates the student records.
* **Flutter Workspace Optimization**:
  * **Action**: Cleaned local cache and build outputs (`flutter clean` inside `mobile_app`), deleted obsolete log files (`flutter_01.log`), resulting in a lightweight, clean mobile workspace.

---

## 3. 🛡️ Maintenance Mode with Super Admin Bypass
* **Goal**: Allow administrators to take the site offline for public users while maintaining full administrative access.

### Key Modifications:
* **File**: `app/Filters/MaintenanceFilter.php` (applied via `app/Config/Filters.php`)
* **Changes**:
  * Intercepts incoming requests if `maintenance_mode` setting is `'true'`.
  * Redirects public traffic to a beautiful, glassmorphic maintenance template.
  * Permits Super Admin access dynamically if a valid bypass key (configured via settings as `maintenance_bypass_key`) is present in the cookie or URL parameter.

---

## 4. 📞 International Phone UI Standardization
* **Goal**: Solve RTL presentation issues and ensure standard, robust phone collections with international country prefix validation.

### Key Modifications:
* **Files**: Various admin, student registration, and registration views.
* **Changes**:
  * Integrated the `intl-tel-input` JavaScript library.
  * Overrode parent RTL directionality on phone fields, locking them to left-to-right (LTR) `Flag-Key-Input` layouts for a premium, standard presentation.

---

## 5. 🚀 Deployment Decoupling & GitHub Optimization
* **Goal**: Stop excessive GitHub Actions token and runner minutes consumption while maintaining standard repository linking and on-demand builds.

### Key Modifications:
* **Removed Automatic PHP Webapp Sync**:
  * **Action**: Completely disconnected automatic FTP deployment triggers in GitHub Actions.
  * **Method**: Created a direct, local PowerShell sync utility leveraging live production credentials (`awamly` / `1722011!Aa!!`) for instant FTP transfers to `102.213.180.22/graya.ly/`.
* **iOS-Only Streamlined Build Workflow**:
  * **File**: `.github/workflows/build.yml`
  * **Changes**:
    * Deleted the high-resource Android APK compilation step.
    * Standardized a 4-minute macOS compilation workflow that generates **ONLY the iOS version** (`sela.ipa`) on manual trigger or push, automatically uploading it to the latest release assets page on GitHub.
* **Workspace Cleanliness**:
  * **Action**: Deleted redundant folders like `node_modules` in the root workspace (since there is no local `package.json` in root) and cleaned old binary files (`sela.ipa`) from source tracking.

---

## 🛠️ Quick Action Sync Scripts (For Developers)

### A. Local PHP Upload (Direct to VPS)
Run the following PowerShell command to instantly synchronize modified backend files without committing to GitHub:
```powershell
function Sync-ToVPS {
    param ([string]$localPath, [string]$remotePath)
    $ftp = [System.Net.FtpWebRequest]::Create("ftp://102.213.180.22/graya.ly/$remotePath")
    $ftp.Method = [System.Net.WebRequestMethods+Ftp]::UploadFile
    $ftp.Credentials = New-Object System.Net.NetworkCredential("awamly", "1722011!Aa!!")
    $bytes = [System.IO.File]::ReadAllBytes($localPath)
    $ftp.ContentLength = $bytes.Length
    $stream = $ftp.GetRequestStream()
    $stream.Write($bytes, 0, $bytes.Length)
    $stream.Close()
    $response = $ftp.GetResponse()
    Write-Host "Synced $localPath -> $remotePath successfully!" -ForegroundColor Green
    $response.Close()
}
```

### B. Accessing the Live iOS Binary
Download the latest compiled iOS `.ipa` file instantly from the release page:
👉 **[Latest iOS Build (sela.ipa)](https://github.com/awam97/sela/releases/download/latest/sela.ipa)**
