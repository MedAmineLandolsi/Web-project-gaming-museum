# Gaming Admin Dashboard - Installation Instructions

## 📁 File Structure

You need to place the files in the correct locations for the styling to work:

### Option 1: Assets Folder Structure (Recommended)
```
ProjetWeb/
├── assets/
│   ├── css/
│   │   └── admin-style.css
│   └── js/
│       └── admin-script.js
├── index.php
├── details.php
├── edit.php
└── voir_reclamation.php
```

### Option 2: Same Directory
```
ProjetWeb/
├── admin-style.css
├── admin-script.js
├── index.php
├── details.php
├── edit.php
└── voir_reclamation.php
```

## 🔧 Setup Instructions

### Step 1: Copy the CSS file
- Copy `admin-style.css` to either:
   - `/projet-web/ProjetWeb/assets/css/admin-style.css` (Option 1)
  - Same directory as your PHP files (Option 2)

### Step 2: Copy the JS file
- Copy `admin-script.js` to either:
   - `/projet-web/ProjetWeb/assets/js/admin-script.js` (Option 1)
  - Same directory as your PHP files (Option 2)

### Step 3: Update PHP files

#### If using Option 1 (Assets folder):
Keep the links as they are in the PHP files:
```html
<link rel="stylesheet" href="/projet-web/ProjetWeb/assets/css/admin-style.css">
<script src="/projet-web/ProjetWeb/assets/js/admin-script.js"></script>
```

#### If using Option 2 (Same directory):
Change the links in ALL PHP files to:
```html
<link rel="stylesheet" href="admin-style.css">
<script src="admin-script.js"></script>
```

## 📝 Files to Update

You need to update the CSS and JS links in these files:
1. ✅ `index.php` (main dashboard)
2. ✅ `details.php` (complaint details)
3. ✅ `edit.php` (edit complaint)
4. ✅ `voir_reclamation.php` (view complaint)

## 🔍 Troubleshooting

### Styling Not Showing?

1. **Check Browser Console** (F12)
   - Look for 404 errors for CSS/JS files
   - This will show you the exact path the browser is trying to load

2. **Verify File Paths**
   - Make sure the CSS file path in your HTML matches where you placed the file
   - Check for typos in filenames

3. **Check File Permissions**
   - Ensure the server can read the CSS and JS files
   - On Linux: `chmod 644 admin-style.css admin-script.js`

4. **Clear Browser Cache**
   - Press Ctrl+Shift+R (or Cmd+Shift+R on Mac) to hard reload

5. **Test CSS Loading**
   - Open the CSS file directly in your browser:
   - `http://yoursite.com/projet-web/ProjetWeb/assets/css/admin-style.css`
   - If you get a 404 error, the path is wrong

## 🎨 Quick Test

Add this to the top of your PHP file to test if CSS is loading:
```html
<style>
    body { background: red; }
</style>
```

If the background turns red, the issue is with the CSS file path.
If nothing happens, there might be a PHP error preventing the page from loading.

## ⚡ Required Fonts

The design uses Google Fonts which are loaded via CDN (already included in the PHP files):
- **Press Start 2P** - For the retro gaming feel
- **VT323** - For monospace terminal-style text

These will load automatically from Google Fonts servers.

## 📞 Common Issues

### Issue: "The page looks the same"
**Solution:** The CSS file is not loading. Check the file path in the `<link>` tag.

### Issue: "Fonts look wrong"
**Solution:** Make sure you have internet connection for Google Fonts to load.

### Issue: "Animations not working"
**Solution:** The JavaScript file is not loading. Check the `<script src="">` path.

### Issue: "Sidebar not showing"
**Solution:** Make sure you're using the updated PHP files that include the sidebar HTML structure.

## ✅ Success Checklist

- [ ] CSS file placed in correct location
- [ ] JS file placed in correct location
- [ ] PHP files have correct `<link>` tag pointing to CSS
- [ ] PHP files have correct `<script>` tag pointing to JS
- [ ] Browser cache cleared
- [ ] No 404 errors in browser console
- [ ] Background is dark (not white)
- [ ] Text is green/colorful (not black)
- [ ] Animations work when hovering over elements

## 🎮 Expected Result

When properly installed, you should see:
- ✨ Dark cyberpunk-style background
- 💚 Neon green primary colors
- 💜 Purple accent colors
- 🎯 Gaming-style fonts (Press Start 2P)
- ✨ Smooth animations on hover
- 📊 Animated stat counters
- 🎨 Glowing borders and effects

If you don't see these, the CSS is not loading properly!
