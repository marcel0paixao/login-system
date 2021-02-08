# LOGIN SYSTEM

# Description
This is a simple login system with CRUD and utilizing In this project is used HTML5, CSS3, JS, VueJs, Bootstrap, PHP7, MySQL, Ajax (Not now, but I'll do a few alterations in future and include), and MVC architecture. In this project, we can create an account, login, edit your personal information and complete delete your account.

## Login
![](/Public/img/printscreenLogin.png)

## Register
![](/Public/img/printscreenRegister.png)

## Home
![](/Public/img/printscreenHome.png)

## Deleting Account
![](/Public/img/printscreenDelete.png)

# How to install
1. Go to App/Connection.php and insert the database info, as host, database name, login and password. By default are:
```
$host = "localhost";
$db = "login";
$user = "root";
$pass = "";
```

2. Create a database with the name of $db, inserting the SQL code bellow:
```
CREATE DATABASE login;

USE login;

CREATE TABLE users (
	id INT NOT NULL PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(60) NOT NULL,
    pass VARCHAR(32) NOT NULL,
    email VARCHAR(100) NOT NULL,
    birthdate DATE NOT NULL
);
```
And now you can use the login system with no problems, creating accounts, editing, logging in and deleting account.

# Problems
Problems that I have:

1. I could not implement Ajax already, I alter this later.
2. The "Forget Password" uses an email code confirmation to change te password, but it's not implemented already.
3. The email confirmation after register is not implemented already.
4. In App/Models/User.php in the function edit, for some reason the bindValue was not working corretly, so I used dots.
5. The file style.css haven't a good organization, I'm going to solve this later.
6. My little VueJS experience make the home.js and login.js with a maybe not so perfectly organization too. 

# Credits
Credits for the background image: Daniele Levis Pelusi.