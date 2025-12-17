#  Gaming Museum - Interactive Digital Platform

##  Project Description

The Gaming Museum is a comprehensive web platform dedicated to preserving and celebrating video game history and culture. This interactive digital museum combines educational content, community engagement, and event management to create a unique experience for gaming enthusiasts.

**Main Objectives:**
- Preserve and showcase video game history through a rich blog and database
- Create an engaged community of gaming enthusiasts
- Organize and manage gaming events, exhibitions, and tournaments
- Provide excellent visitor support through an intelligent complaint management system
- Offer an interactive and educational experience for all ages

**Problem Solved:** Traditional gaming museums are limited by physical space and location. Our digital platform makes gaming history accessible to everyone worldwide, combining museum-quality content with modern community features and AI-powered assistance.

## Table of Contents

- [Installation](#installation)
- [Usage](#usage)
- [Museum Features](#museum-features)
- [Technologies Used](#technologies-used)
- [Project Structure](#project-structure)
- [Contributing](#contributing)
- [License](#license)

## Installation

### Prerequisites

Make sure you have the following tools installed on your machine:

* **PHP** (version 7.4 or higher) - [Download PHP](https://www.php.net/downloads)
* **MySQL** (version 5.7 or higher)
* **XAMPP** or **WAMP** for local development environment
* **Git** for version control
* **Visual Studio Code** (recommended) or any other code editor

### Installation Steps

1. Clone the repository:

```bash
git clone https://github.com/your-username/web-project-gaming-museum.git
cd web-project-gaming-museum
```

2. Configure the database environment:

* Start XAMPP and launch Apache and MySQL
* Access phpMyAdmin via `http://localhost/phpmyadmin`
* Create a new database named `gaming_museum`
* Import the provided SQL file:

```bash
mysql -u root -p gaming_museum < database/schema.sql
```

3. Configure connection settings:

* Open the file `config.php`
* Modify the connection parameters according to your configuration:

```php
<?php
  class config {
    private static $pdo = NULL;

    public static function getConnexion() {
      if (!isset(self::$pdo)) {
        try{

          self::$pdo = new PDO('mysql:host=localhost;dbname=gaming_museum1', 'root', '', // Insert your domain, database name, username and password here
          [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]);
        }catch(Exception $e){
          die('Erreur: '.$e->getMessage());
        }
      }
      return self::$pdo;
    }
  } 
?>
```

4. Install dependencies (if applicable):

```bash
composer install
```

5. Launch the development server:

```bash
php -S localhost:8000
```

6. Access the application via your browser:

```
http://localhost:8000
```

## Usage

### Installing PHP

If PHP is not installed on your system, follow these instructions:

**For Windows:**
* Download XAMPP from [apachefriends.org](https://www.apachefriends.org)
* Install XAMPP which includes PHP, MySQL, and Apache
* PHP will be available in the `C:\xampp\php` folder

**For macOS:**
* Use Homebrew: `brew install php`
* Or download MAMP from [mamp.info](https://www.mamp.info)

**For Linux (Ubuntu/Debian):**
```bash
sudo apt update
sudo apt install php php-mysql php-mbstring php-xml
```

### Getting Started

1. **Create your visitor account**: Register on the platform to access all museum features
2. **Explore the gaming history**: Browse through decades of gaming evolution in our blog section
3. **Join the community**: Connect with fellow gaming enthusiasts and share your experiences
4. **Participate in events**: Register for virtual exhibitions, tournaments, and special gaming events
5. **Browse the game database**: Discover detailed information about thousands of video games

### Administrator Access

Museum curators and administrators can access the management panel:
* **URL**: `http://localhost:8000/admin`
* **Default credentials**: 
  - Email: `admin@gmail.com`
  - Password: `admin` 

## Museum Features
###  User Management System
**Module Lead: [Med Amine Landolsi]**

Comprehensive user authentication and profile management system that handles all visitor registrations, accounts, and activity tracking throughout the museum platform.

**Features:**
-  **Registration System**: 
  - Multi-field registration forms with validation
  - Email verification and account activation
  - Secure password hashing and storage
-  **User Authentication**:
  - Secure login/logout functionality
  - Session management and security
  - Password recovery and reset options
-  **Database Management**:
  - Store user information in MySQL database
  - Efficient data retrieval and updates
  - Data integrity and validation
-  **Activity Tracking**:
  - User action logging and history
  - Participation tracking across all modules
  - Comprehensive user activity dashboard
-  **Custom Dashboard**:
  - Personalized user dashboard with unique visual style
  - Centralized view of user data, activities, and statistics
  - Simple and user-friendly navigation
-  **User Profile Management**:
  - Edit personal information and preferences
  - Upload profile pictures and customize appearance
- **Diversity of Authentication Methods**:
  - Google OAuth integrated system
  - 2FA page for extra verification
  - included captcha pattern

###  Gaming History Blog & Archives
**Module Lead: Ilef Karoui**

A rich content management system documenting the evolution of video games, featuring articles about gaming history, industry milestones, and cultural impact.

**Features:**
-  **AI Museum Guide Chatbot**: Interactive assistant helping visitors discover relevant articles and content based on their interests
-  **Advanced Content Discovery**:
  - Filter by gaming era (1970s-2020s)
  - Search by console, genre, or developer
  - Tag-based navigation for topics
-  **Personalized Experience Settings**:
  - Customize content preferences
  - Notification settings for new exhibitions
  - Content moderation tools for curators
-  **Visitor Analytics Dashboard**: Track article popularity, visitor engagement, and trending topics

###  Museum Events & Exhibitions
**Module Lead: [Selim Acchi]**

Comprehensive system for organizing virtual and physical museum events, including game launches, retro gaming nights, tournaments, and special exhibitions.

**Features:**
-  **Event Creation and Management**: Create detailed event pages with schedules, descriptions, and multimedia
-  **Advanced Participant Management**: Track RSVPs, manage waitlists, and organize attendee groups
-  **Registration System**: Validated forms capturing visitor information and preferences
-  **Participation History**: Track visitor attendance and engagement across multiple events
-  **Custom Dashboard**: Unique visual interface for event organizers with real-time statistics
-  **Secure Data Management**: All visitor and event data stored securely in MySQL database


###  Gaming Community Hub
**Module Lead: Nour Touhemi**

A vibrant social space where gaming enthusiasts can connect, share memories, and discuss gaming culture.

**Features:**
-  **Member Profiles**: Detailed gaming profiles with favorite games, achievements, and gaming history
- **Gaming Communities**: 
  - Era-specific communities (Retro, Modern, Indie)
  - Platform-specific groups (Nintendo, PlayStation, Xbox, PC)
  - Genre-based discussion forums (RPG, FPS, Strategy, etc.)
- **AI-Powered Content Generation**: 
  - Auto-suggest discussion topics based on trending games
  - Generate gaming-related posts from keywords
  - AI writing assistance for reviews and articles
- **Social Publishing System**:
  - Share gaming memories and screenshots
  - Like, comment, and share community content
  - Personalized feed based on interests
- **Gaming Network**: Connect with other gamers and build your gaming circle

### 🛠️ Visitor Support & Feedback System
**Module Lead: Nedra Ouihibi**

Intelligent complaint and feedback management system ensuring excellent visitor experience.

**Features:**
- **Multilingual Support**: 
  - Automatic translation for international visitors
  - Real-time message translation
  - Multi-language interface adaptation
- **Smart Complaint Handling**:
  - Automatic categorization (technical issues, content feedback, event inquiries)
  - Priority-based routing to appropriate staff
  - Intelligent agent assignment
- **Guided Feedback Form**: 
  - Adaptive form based on issue type
  - Step-by-step problem description
  - AI-suggested solutions before ticket creation
- **Proactive Issue Detection**:
  - Automatic ticket generation for recurring problems
  - Pattern recognition for common visitor issues
  - System log integration for technical problems
- **Support Analytics Dashboard**:
  - Response time metrics
  - Visitor satisfaction ratings
  - Issue resolution tracking and reporting

### Video Game Database & Catalog
**Module Lead: [Wessim Hannechi]**

Comprehensive database documenting thousands of video games throughout history, serving as the museum's core archive.

**Features:**
- **Extensive Game Catalog**: 
  - Detailed game profiles with release info, developers, and platforms
  - High-quality screenshots and cover art
  - Historical context and cultural significance
- **Community Reviews**: Visitor ratings and reviews for each game
- **Advanced Search System**:
  - Filter by year, platform, genre, developer
  - Full-text search across game descriptions
  - Recommendation engine based on preferences
- **Gaming Statistics**: 
  - Most popular games by era
  - Platform evolution timelines
  - Genre trends across decades
- **Personal Collections**: 
  - Create favorite game lists
  - Track games you've played
  - Build custom gaming timelines

##  Technologies Used

### Frontend
* **HTML5** - Semantic structure for museum content
* **CSS3** - Modern styling with museum-themed design
* **JavaScript** - Interactive galleries and dynamic content

### Backend
* **PHP** (7.4+) - Server-side logic and museum operations
* **MySQL** - Relational database for games, events, and user data

### Development Tools
* **XAMPP** - Local development environment
* **Visual Studio Code** - Primary code editor
* **Git & GitHub** - Version control and team collaboration
* **phpMyAdmin** - Database administration and management

### APIs and Libraries
* **Bootstrap** (optional) - Responsive CSS framework
* **jQuery** (optional) - Enhanced DOM manipulation
* **AI/NLP APIs** - Chatbot and content generation features
* **Translation API** - Multilingual support system

## Project Structure

```
─projet-web
    ├───gaming_museum
    │   ├───controller
    │   ├───model
    │   ├───uploads
    │   └───view
    │       ├───backoffice
    │       └───frontoffice
    ├───projet
    │   ├───.git
    │   │   ├───cursor
    │   │   │   └───crepe
    │   │   │       └───aa999b04ee0125ed600c9c21ac16d43cde12053e
    │   │   ├───hooks
    │   │   ├───info
    │   │   ├───logs
    │   │   │   └───refs
    │   │   │       ├───heads
    │   │   │       └───remotes
    │   │   │           └───origin
    │   │   ├───objects
    │   │   │   ├───info
    │   │   │   └───pack
    │   │   ├───rebase-merge
    │   │   └───refs
    │   │       ├───heads
    │   │       ├───remotes
    │   │       │   └───origin
    │   │       └───tags
    │   ├───api
    │   ├───controller
    │   ├───model
    │   ├───tools
    │   ├───uploads
    │   └───view
    │       ├───backoffice
    │       │   ├───communautes
    │       │   └───publications
    │       ├───frontoffice
    │       │   ├───auth
    │       │   ├───communautes
    │       │   └───publications
    │       └───shared
    ├───ProjetWeb
    │   ├───api
    │   ├───assets
    │   │   ├───css
    │   │   ├───img
    │   │   └───js
    │   ├───config
    │   ├───Controller
    │   ├───data
    │   ├───Model
    │   └───View
    │       ├───back
    │       └───front
    ├───projetweb12
    │   ├───.git
    │   │   ├───hooks
    │   │   ├───info
    │   │   ├───logs
    │   │   │   └───refs
    │   │   │       ├───heads
    │   │   │       └───remotes
    │   │   │           └───origin
    │   │   ├───objects
    │   │   │   ├───info
    │   │   │   └───pack
    │   │   └───refs
    │   │       ├───heads
    │   │       ├───remotes
    │   │       │   └───origin
    │   │       └───tags
    │   ├───config
    │   ├───controllers
    │   ├───models
    │   └───views
    │       ├───assets
    │       ├───Backoffice
    │       └───Frontoffice
    └───projet_gaming
        ├───.git
        │   ├───hooks
        │   ├───info
        │   ├───logs
        │   │   └───refs
        │   │       ├───heads
        │   │       └───remotes
        │   │           └───origin
        │   ├───objects
        │   │   ├───info
        │   │   └───pack
        │   └───refs
        │       ├───heads
        │       ├───remotes
        │       │   └───origin
        │       └───tags
        ├───admin
        │   ├───evenements
        │   ├───participations
        │   └───views
        │       └───back
        ├───assets
        │   ├───css
        │   └───js
        ├───config
        ├───controllers
        ├───database
        ├───models
        └───views
            ├───back
            └───front
```

### Contributors

We welcome contributions from gaming enthusiasts, developers, and historians! Here's how you can help preserve gaming history.
- [Med_Amine_Landolsi](https://github.com/MedAmineLandolsi) - User management system
- [Selim_Acchi](https://github.com/selimaschi) - Event management system
- [Ilef_Karoui](https://github.com/ilefkaroui) - blog, posts and news management system
- [Wissem_hannechi](https://github.com/WissemHaa) - Video game database management
- [Nedra_Ouihibi](https://github.com/nedra-2) - Refund and customer support management
- [Nour_Touhemi](https://github.com/nour-touhemi) - Games' Community Management 

### How to Contribute?

1. **Fork the project**: Click the "Fork" button at the top of this page

2. **Clone your fork**:
```bash
git clone https://github.com/your-username/web-project-gaming-museum.git
```

3. **Create a feature branch**:
```bash
git checkout -b feature/new-gaming-feature
```

4. **Make your changes** and commit:
```bash
git add .
git commit -m "Add feature: detailed description"
```

5. **Push to your fork**:
```bash
git push origin feature/new-gaming-feature
```

6. **Create a Pull Request**: Submit your changes for review via GitHub

### Ways to Contribute

* **Content**: Add gaming history articles, game entries, or event descriptions
* **Code**: Implement new features or fix bugs
* **Design**: Improve UI/UX with museum-themed designs
* **Translation**: Help make the museum accessible in more languages
* **Documentation**: Improve guides and documentation
* **Testing**: Report bugs and test new features

### Contribution Guidelines

* Follow PHP PSR-12 coding standards
* Write clear, documented code with comments
* Test all changes thoroughly before submitting
* Ensure backward compatibility with existing features
* Use descriptive commit messages
* Respect gaming history accuracy in content contributions

### Reporting Issues

Found a bug or have a suggestion? Open an issue with:
* Clear description of the problem or suggestion
* Steps to reproduce (for bugs)
* Expected vs actual behavior
* Screenshots or examples if applicable
* Your environment (browser, PHP version, etc.)

## License

This project is licensed under the **MIT License** - see the [LICENSE](LICENSE) file for full details.

**You are free to:**
* Use this code for commercial or non-commercial purposes
* Modify and adapt the code to your needs
* Distribute and share the code
* Use privately for personal projects

**Under the condition that:**
* You include the MIT license in any distribution
* You credit the original authors
* You provide the license and copyright notice

---

*** Preserving Gaming History, One Line of Code at a Time**

*Developed with ❤️ by gaming enthusiasts for the Web Technologies course 2025-2026*

*Have questions? Open an issue on GitHub or contact our development team!*
