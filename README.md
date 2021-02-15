# LOGIN SYSTEM

# Description
This is a simple login system with CRUD, project uses HTML5, CSS3, JS, VueJs, Bootstrap, PHP7, MySQL, Ajax (Not now, but I'll do a few alterations in future and include), and MVC architecture. In this project, we can create an account, login, edit your personal information and complete delete your account.

## Login
![](/Public/img/printscreenLogin.png)

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

CREATE TABLE email_request(
    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	email VARCHAR(100) NOT NULL,
    hash VARCHAR(32) NOT NULL,
    type VARCHAR(20) NOT NULL,
    status INT(10) NOT NULL
);
```
And now you can use the login system with no problems, creating accounts, editing, logging in and deleting account.

# Problems
Problems that I had:

1. I could not implement Ajax already, I alter this later.
2. Login using Facebook, Gmail or Twitter is not implemented.
3. In App/Models/User.php in the function edit, for some reason the bindValue was not working corretly, so I used dots.
4. The file style.css haven't a good organization, I'm going to solve this later.
5. My little VueJS experience make the home.js and login.js with a maybe not so perfectly organization too. 

# Credits
Credits for the background image: Daniele Levis Pelusi.

# Author
I'm Marcelo da Paixão, a student of computer science and a beginner full-stack developer.

<a href="https://github.com/marcel0paixao">
 <img style="border-radius: 50%;" src="https://avatars.githubusercontent.com/u/74371070?s=460&u=dc96807a34bd825b3ee1b12178e7c852ea1a7131&v=4" width="100px;" alt=""/>
 <br />
 <sub><b>Marcelo da Paixão</b></sub></a>

[![Linkedin Badge](https://img.shields.io/badge/-Marcelo-blue?style=flat-square&logo=Linkedin&logoColor=white&link=https://www.linkedin.com/in/marcelo-da-paix%C3%A3o-silva-123677194/)](https://www.linkedin.com/in/marcelo-da-paix%C3%A3o-silva-123677194/) 
[![Gmail Badge](https://img.shields.io/badge/-marceloht461@gmail.com-c14438?style=flat-square&logo=Gmail&logoColor=white&link=mailto:marceloht461@gmail.com)](marceloht461@gmail.com)

<br />
Or per telephone: 
<br />
+55 11 98971-9266
<br />
+55 11 2359-3783
