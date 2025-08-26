# 🧴 Crafted Well - Personalized Skincare Platform

[![Live Application](https://img.shields.io/badge/🌐_Live_App-Visit_Now-2563eb?style=for-the-badge)](https://crafted-well-laravel.up.railway.app)
[![Laravel](https://img.shields.io/badge/Laravel-11-ff2d20?style=flat-square&logo=laravel)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.2+-777bb4?style=flat-square&logo=php)](https://php.net)
[![Railway](https://img.shields.io/badge/Deployed_on-Railway-0B0D0E?style=flat-square&logo=railway)](https://railway.app)

> **🔗 Access the live application: [https://crafted-well-laravel.up.railway.app](https://crafted-well-laravel.up.railway.app)**

## 🌟 About Crafted Well

Crafted Well is a revolutionary **personalized skincare platform** that creates custom serum formulations tailored to individual skin needs. Using advanced skin analysis and AI-driven recommendations, we deliver bespoke skincare solutions that adapt to your unique skin profile.

### ✨ Key Features

- 🔬 **AI-Powered Skin Analysis** - Advanced questionnaire system for precise skin profiling
- 🧪 **Custom Serum Formulation** - Personalized products based on individual skin needs
- 👤 **User Profile Management** - Comprehensive skin health tracking and history
- 🛒 **E-commerce Integration** - Seamless shopping cart and checkout experience
- 📊 **Admin Dashboard** - Complete product and user management system
- 📱 **Responsive Design** - Beautiful UI that works on all devices

## 🚀 Live Demo

**Main Application:** [https://crafted-well-laravel.up.railway.app](https://crafted-well-laravel.up.railway.app)

### Demo Accounts
- **Admin Panel:** `/admin/login`
  - Email: `admin@craftedwell.com`
  - Password: `admin123`
- **User Account:** Register at `/register` or use the survey system

## 🏗️ Technology Stack

### Backend
- **Framework:** Laravel 11
- **Language:** PHP 8.2+
- **Database:** MySQL
- **Authentication:** Laravel Sanctum
- **Storage:** Local filesystem with cloud-ready architecture

### Frontend
- **Styling:** Tailwind CSS
- **JavaScript:** Vanilla JS with modern ES6+
- **Icons:** Font Awesome 6
- **Animations:** CSS transitions and keyframes

### Infrastructure
- **Hosting:** Railway
- **Environment:** Production-ready with HTTPS
- **Database:** Railway MySQL
- **File Storage:** Optimized for cloud deployment

## 🛠️ Installation & Setup

### Prerequisites
- PHP 8.2 or higher
- Composer
- Node.js & npm
- MySQL database

### Local Development

1. **Clone the repository**
   ```bash
   git clone https://github.com/yourusername/crafted-well-laravel.git
   cd crafted-well-laravel
   ```

2. **Install PHP dependencies**
   ```bash
   composer install
   ```

3. **Install Node dependencies**
   ```bash
   npm install
   ```

4. **Environment setup**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

5. **Configure your `.env` file**
   ```env
   APP_NAME="Crafted Well"
   APP_URL=http://localhost:8000
   
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=crafted_well
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

6. **Run migrations and seed data**
   ```bash
   php artisan migrate --seed
   ```

7. **Start the development server**
   ```bash
   php artisan serve
   ```

8. **Compile assets (in another terminal)**
   ```bash
   composer run dev
   ```

Visit `http://localhost:8000` to see your local application!

## 🎯 Core Functionality

### For Users
- **Skin Analysis Survey** - Comprehensive questionnaire to determine skin type and concerns
- **Custom Product Creation** - AI-generated serum formulations based on survey results
- **Profile Management** - Track skin progress and product history
- **Shopping Experience** - Add custom products to cart and complete purchases
- **Order History** - View past orders and reorder favorite formulations

### For Administrators
- **Product Management** - Create, edit, and manage base formulations
- **User Analytics** - Monitor user engagement and skin analysis trends
- **Order Processing** - Handle customer orders and fulfillment
- **Content Management** - Update product information and pricing
- **System Health** - Monitor application performance and usage

## 📁 Project Structure

```
crafted-well-laravel/
├── app/
│   ├── Http/Controllers/     # API and web controllers
│   ├── Models/              # Eloquent models
│   ├── Livewire/            # Interactive components
│   └── Http/Middleware/     # Custom middleware
├── database/
│   ├── migrations/          # Database schema
│   └── seeders/            # Sample data
├── resources/
│   ├── views/              # Blade templates
│   ├── css/                # Styling
│   └── js/                 # Frontend JavaScript
├── routes/
│   ├── web.php             # Web routes
│   └── api.php             # API routes
└── public/                 # Public assets
```

## 🔐 Security Features

- **CSRF Protection** - All forms protected against cross-site request forgery
- **Authentication** - Secure user login and registration system
- **Role-based Access** - Admin and user role separation
- **Input Validation** - Comprehensive server-side validation
- **HTTPS Enforcement** - Secure communication in production
- **SQL Injection Prevention** - Eloquent ORM protection

## 🚀 Deployment

### Railway Deployment
This application is optimized for Railway deployment:

1. **Connect your GitHub repository** to Railway
2. **Set environment variables** in Railway dashboard
3. **Configure database** using Railway's MySQL addon
4. **Deploy** - Railway handles the rest automatically!

### Environment Variables for Production
```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.com
FORCE_HTTPS=true
```

## 🤝 Contributing

We welcome contributions to Crafted Well! Here's how you can help:

1. **Fork** the repository
2. **Create** a feature branch (`git checkout -b feature/amazing-feature`)
3. **Commit** your changes (`git commit -m 'Add amazing feature'`)
4. **Push** to the branch (`git push origin feature/amazing-feature`)
5. **Open** a Pull Request

### Development Guidelines
- Follow PSR-12 coding standards
- Write meaningful commit messages
- Add tests for new features
- Update documentation as needed

## 👨‍💻 Developer

**Zuhri Ashroff** - Full Stack Developer  
*Passionate about creating innovative solutions*

## 📞 Support & Contact

- **🌐 Website:** [https://crafted-well-laravel.up.railway.app](https://crafted-well-laravel.up.railway.app)
- **👨‍💻 Developer:** Zuhri Ashroff
- **📧 Email:** support@craftedwell.com
- **🐛 Issues:** [GitHub Issues](https://github.com/yourusername/crafted-well-laravel/issues)

## 📄 License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for details.

## 🙏 Acknowledgments

- **Zuhri Ashroff** - Lead Developer and Creator of Crafted Well
- **Laravel Team** - For the amazing framework
- **Tailwind CSS** - For the utility-first CSS framework
- **Railway** - For seamless deployment and hosting
- **Font Awesome** - For the beautiful icons
- **Open Source Community** - For inspiration and contributions

---

<p align="center">
  <strong>Developed with ❤️ by Zuhri Ashroff</strong><br>
  <em>Built for beautiful, personalized skincare</em>
</p>

<p align="center">
  <a href="https://crafted-well-laravel.up.railway.app">🌐 Visit Live Application</a> •
  <a href="#installation--setup">🛠️ Setup Guide</a> •
  <a href="#contributing">🤝 Contribute</a>
</p>
