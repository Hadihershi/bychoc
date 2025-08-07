# 🍫 BeyChoc - Premium Chocolate Shop Website

A fully responsive, modern website for BeyChoc chocolate shop built with HTML, CSS, JavaScript, and PHP with complete admin panel functionality.

## ✨ Features

### 🎨 **Modern Design**
- Clean, professional chocolate-themed design
- Custom color palette: `#4C3D19`, `#354024`, `#889063`, `#CFBB99`, `#E5D7C4`
- Smooth animations and transitions throughout
- Professional typography using Poppins font

### 📱 **Fully Responsive**
- Mobile-first responsive design
- Works perfectly on all devices (phones, tablets, desktops)
- Hamburger menu for mobile navigation
- Optimized layouts for different screen sizes
- Touch-friendly interface

### 🏪 **Product Showcase**
- Dynamic product display with 6 categories:
  - White Chocolate
  - Milk Chocolate
  - Dark Chocolate
  - Light Chocolate
  - Bars
  - Packages
- Real-time search functionality (by name or code)
- Category filtering
- Product detail modals
- Responsive product grid

### 🎞️ **Interactive Banner**
- Auto-rotating slideshow with 3 slides
- Smooth fade transitions
- Call-to-action buttons
- Fully customizable content

### 🔐 **Admin Panel**
- Secure admin login system
- Session management with database tracking
- Complete product CRUD operations:
  - Add new products
  - Edit existing products
  - Delete products
  - Image upload functionality
- Weight field for Bars and Packages
- Dashboard with statistics
- Auto-logout and session cleanup

### 📞 **Contact Integration**
- WhatsApp integration
- Instagram link
- Hover animations
- Easy customization

## 🚀 Quick Start

1. **Start XAMPP**: Ensure Apache and MySQL are running
2. **Access Website**: Go to `http://localhost/bych/`
3. **Admin Login**: Click "Admin" and use credentials:
   - Username: `Lana Moghnieh`
   - Password: `123454321`

The database and sample products are created automatically on first visit!

## 📁 Project Structure

```
bych/
├── index.html                 # Main website
├── setup_instructions.html    # Detailed setup guide
├── assets/
│   ├── css/
│   │   └── style.css         # Complete styling & responsive design
│   ├── js/
│   │   └── script.js         # Frontend functionality
│   └── images/               # Images directory
│       ├── products/         # Product images
│       └── placeholder.jpg   # Default placeholder
├── admin/                    # Admin panel
│   ├── login.php            # Admin login page
│   ├── dashboard.php        # Product management dashboard
│   ├── add_product.php      # Add new products
│   ├── edit_product.php     # Edit products
│   ├── logout.php           # Logout handler
│   ├── process_login.php    # Login processing
│   └── session_check.php    # Session management
├── api/
│   └── get_products.php     # Products API endpoint
└── config/
    └── database.php         # Database connection & setup
```

## 🛠️ Technical Specifications

### **Frontend**
- **HTML5**: Semantic structure with accessibility
- **CSS3**: Flexbox/Grid layouts, animations, variables
- **JavaScript**: ES6+ features, async/await, DOM manipulation
- **Responsive**: Mobile-first approach with breakpoints
- **Performance**: Optimized animations, lazy loading support

### **Backend**
- **PHP 7.4+**: Object-oriented programming
- **MySQL**: Relational database with PDO
- **Security**: Prepared statements, session management
- **File Upload**: Secure image handling
- **API**: RESTful endpoint structure

### **Database Schema**
```sql
products:
- id (PRIMARY KEY)
- name, code (UNIQUE)
- description, image
- category (ENUM)
- weight (for bars/packages)
- timestamps

admin_sessions:
- session tracking
- expiry management
- security features
```

## 🎯 Customization Guide

### **Logo & Branding**
Replace `assets/images/logo.png` with your logo

### **Banner Images**
Add your images as:
- `assets/images/banner1.jpg`
- `assets/images/banner2.jpg`
- `assets/images/banner3.jpg`

### **Contact Information**
Update links in `index.html`:
- Line 113: WhatsApp number
- Line 118: Instagram username

### **Colors**
All colors are defined as CSS variables in `:root` for easy customization

### **Content**
- Banner text and CTAs in `index.html`
- Section headers and descriptions
- Footer information

## 📋 Admin Features

### **Product Management**
- ✅ Add products with image upload
- ✅ Edit all product information
- ✅ Delete products with confirmation
- ✅ Category-based weight fields
- ✅ Real-time updates

### **Security**
- ✅ Secure login with session tracking
- ✅ Database session management
- ✅ Auto-logout on inactivity
- ✅ SQL injection protection
- ✅ File upload validation

### **User Experience**
- ✅ Intuitive dashboard design
- ✅ Responsive admin panel
- ✅ Loading states and feedback
- ✅ Error handling
- ✅ Success notifications

## 🔧 Requirements

- **XAMPP** (or LAMP/WAMP)
- **PHP 7.4+**
- **MySQL 5.7+**
- **Modern web browser**

## 📱 Responsive Breakpoints

- **Mobile**: < 768px
- **Tablet**: 768px - 1024px
- **Desktop**: > 1024px
- **Large Desktop**: > 1200px

## 🎨 Design System

### **Typography**
- Primary Font: Poppins
- Weights: 300, 400, 500, 600, 700
- Responsive font scaling

### **Color Palette**
- **Primary**: `#4C3D19` (Chocolate Brown)
- **Secondary**: `#354024` (Deep Green)
- **Accent**: `#889063` (Olive)
- **Light**: `#CFBB99` (Cream)
- **Background**: `#E5D7C4` (Light Cream)

### **Shadows & Effects**
- Consistent shadow system
- Hover animations
- Smooth transitions
- Modern blur effects

## 🚀 Performance Features

- **Optimized Images**: Responsive image handling
- **Lazy Loading**: Intersection Observer API
- **Debounced Search**: Smooth user experience
- **Efficient DOM**: Minimal reflows and repaints
- **Clean Code**: Well-organized and commented

## 📞 Support

The codebase is well-documented and organized for easy maintenance and customization. All major components are modular and clearly commented.

## 🎉 Ready to Use!

Your BeyChoc website is production-ready with:
- ✅ All requested features implemented
- ✅ Responsive design across all devices
- ✅ Modern animations and smooth UX
- ✅ Secure admin panel
- ✅ Professional appearance
- ✅ Easy customization options

**Enjoy your new chocolate shop website!** 🍫
