# Laravel Vue MongoDB Task Manager

The Laravel Vue MongoDB Task Manager is a web application designed to help you efficiently manage your tasks and projects. Built using the Laravel PHP framework, Vue.js, and MongoDB as the database, this task manager provides a modern and responsive user interface along with a powerful backend.

## Table of Contents

- [Project Features](#project-features)
- [Project Screenshots](#project-screenshots)
- [Installation](#installation)
- [Usage](#usage)
- [Contributing](#contributing)
- [License](#license)

## Project Features

- **Task Management**: Easily create, update, and delete tasks. Assign due dates, set priorities, and track progress.
- **Project Organization**: Group tasks into projects for better organization and management.
- **Collaboration**: Collaborate with team members by assigning tasks and adding comments.
- **User Authentication**: Secure user authentication to protect your data.
- **User Authorization**: Public tasks can be seen/edited by everyone, private tasks can be seen/edited only by the team, the owner or the assignee.
- **Real-time Updates and Comunication**: Experience real-time updates and comments using web sockets (Pusher) to ensure everyone stays up to date with the latest changes. Users will get a toast when someone commented on a task that they belong to it.
- **Searching and Sorting**: Quickly find tasks using advanced searching and sorting options.
- **Reminders**: Set reminders for tasks assignees. Tasks reminders will be sent to the assignee's email everyday at 9AM.
- **Dashboard**: A complete and interactive dashboard to keep up with your tasks.
- **Responsive Design**: Access and manage your tasks from any device, whether it's a desktop computer, tablet, or smartphone.

## Project Screenshots
![Dashboard](./resources/images/dashboard.png)

![Tasks](./resources/images/tasks.png)

![Projects](./resources/images/projects.png)

![Users](./resources/images/users.png)

![Search](./resources/images/search.png)

![Project](./resources/images/project.png)

![User](./resources/images/user.png)

![EditTask](./resources/images/editTask.png)

![Comments](./resources/images/comments.png)

## Installation

To install and run the Laravel Vue MongoDB Task Manager, follow these steps:

1. Clone the repository: `git clone https://github.com/your-username/laravel-vue-mongodb-task-manager.git`
2. Navigate to the project directory: `cd laravel-vue-mongodb-task-manager`
3. Install PHP dependencies: `composer install`
4. Install JavaScript dependencies: `npm install`
5. Create a `.env` file by duplicating `.env.example` and updating the necessary configuration details, including your MongoDB connection settings and Pusher App settings.
6. Generate an application key: `php artisan key:generate`
7. Run the database migrations: `php artisan migrate`
8. Set the scheduler on your server: `* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1` for Linux or `* * * * * cd /path-to-your-project && php artisan schedule:run > NUL` for Windows
9. Compile the assets: `npm run dev` (for development) or `npm run build` (for production)
10. Start the development server: `php artisan serve`

Make sure to review the official Laravel and Vue.js documentation for detailed information on system requirements and additional configuration options.

This project comes with factories and seeds, if you want to test it, you can run it fresh `php artisan migrate:fresh --seed` or `php artisan db:seed`.

## Usage

Once the installation process is complete, open the application in your web browser. You will be prompted to create a new account or log in if you already have one. After logging in, you can start creating projects, adding tasks, and managing your workflow efficiently. Users are grouped by Squads, any user from the Squad can invite a new user. You can join a specific Squad if you have their Id.

## Contributing

Contributions to the Laravel Vue MongoDB Task Manager project are welcome! To contribute, follow these steps:

1. Fork the repository.
2. Create a new branch for your feature or bug fix: `git checkout -b feature/your-feature-name` or `git checkout -b bugfix/your-bug-fix-name`
3. Commit your changes: `git commit -m "Add your commit message"`
4. Push to the branch: `git push origin your-branch-name`
5. Submit a pull request to the main repository.
Please follow the existing code style and conventions and ensure that your contributions are well-tested.

## License

The Laravel Vue MongoDB Task Manager is open-source software licensed under the [MIT License](https://opensource.org/licenses/MIT). See the `LICENSE` file for more details.

## Acknowledgments

Special thanks to the Laravel, Vite, Vue.js, Axios, Pusher, MongoDB, Tailwind, Iconify, ChartJs and Html2canvas communities for their fantastic tools and resources that made this project possible.

