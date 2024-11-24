# OpenComunidad

OpenComunidad is a community management platform built during a hackathon that enables neighborhoods and residential communities to better organize, communicate, and make collective decisions.

## 🏆 Hackathon Project

This project was built during Platanus Hack 24 with the goal of optimizing real estate investments through better community management. By fostering healthier, more organized communities, the platform helps:
- Reduce vacancy rates through improved resident satisfaction
- Increase rent retention by building stronger community bonds
- Lower operational costs via efficient communication
- Minimize risks through better community oversight and decision-making

While built in a short time frame, we aimed to create a foundation that benefits both property investors and residents alike.


## 🌟 Features

### Community Management
- **Multiple Communities**: Users can join and participate in different residential communities
- **Role-Based Access**: Supports different roles (Admin, Manager, Neighbor) with varying permissions
- **Custom Branding**: Communities can have their own logos and banners

### Posts & Communication
- **Rich Text Posts**: Create and share posts with markdown support
- **Categorization**: Organize posts by categories for better navigation
- **Approval System**: Moderation system for post approval by community admins
- **AI-Powered Content Moderation**:
  - LLM-based content analysis ensures constructive communication
  - Automatic detection and filtering of hostile or threatening content
  - Promotes balanced and solution-oriented discussions
- **Interactive Elements**: 
  - Upvoting system
  - Follow/unfollow posts for updates
  - Comment system for discussions

### Community Decision Making
- **Poll System**: 
  - Create polls with multiple options
  - Real-time vote counting
  - Countdown timers for voting deadlines
  - Results visualization with animated progress bars
  - Admin-only early results viewing

### User Experience
- **Responsive Design**: Works seamlessly across desktop and mobile devices
- **Real-time Updates**: Dynamic content updates without page refreshes
- **Intuitive Navigation**: Easy-to-use interface for all user types

## 🛠 Tech Stack

- **Backend**: Laravel PHP Framework
- **Frontend**: React with TypeScript
- **State Management**: Inertia.js
- **Styling**: Tailwind CSS
- **Rich Text**: TipTap Editor
- **Animations**: Framer Motion
- **File Storage**: R2 Compatible Storage

## 🚀 Getting Started

1. Clone the repository
2. Set up your environment variables
3. Install dependencies
```
make vendor-install
```
4. Start the containers
```
make run
```
5. Run migrations
```
make migrate
```
6. Seed the database
```
make seed
```


## 🤝 Contributing

This project was built during a hackathon but we welcome contributions! Feel free to:
- Report bugs
- Suggest features
- Submit pull requests

## 📝 License

[MIT License](LICENSE.md)


---

Built with ❤️ for communities
