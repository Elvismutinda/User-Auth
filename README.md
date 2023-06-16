# User Authentication

This is a repository containing the implementation of user authentication when it comes to things such as login, registration and the process of reseting their password.

The code is written in PHP and covers website attacks such as SQL injections and brute forces.

Therefore this is my personal implementation of how I believe user authentication would be best handled. 

```There is always room for improvement, let me know what you would do differently and why.```

LET'S BEGIN!

## Table of Contents

  1. [Account Creation](#account-creation)
  2. [Login](#login)
  3. [Forgot Password](#forgot-pass)

### Account Creation <a name="account-creation"> </a>

Creating an account in any system is an important feature to enable potential users access certain features of your system.

#### 1. Email Security Measures
Here I implemented security features that would handle sanitizing and validating user emails in the process of creating an account.

The email was sanitized using the ```filter_var``` function together with ```FILTER_SANITIZE_EMAIL``` filter.

To validate user inputs of the email format together with ensuring that the other fields are not empty, you can refer to the code below:
```php
<?php
  // Validate user inputs
  if(empty($name) || empty($email) || empty($password)){
      echo "Failed to create account";
      exit;
  }
  // Validate email format
  if(!filter_var($email, FILTER_VALIDATE_EMAIL)){
      echo "Invalid email format";
      exit;
  }
?>
```

#### 2. Password Security Measures
The password was then hashed using ```PASSWORD_BCRYPT``` since it includes a unique salt for each password hash making it more secure than md5.

#### What's a Salt?

A salt is a random value added to the password before hashing which increases security by making it extremely difficult to crack password using precomputed rainbow tables or dictionary attacks.

Moreover, password strength implementation was done to ensure users choose strong passwords.
  
Things such as password minimum length, combination of uppercase, lowercase letters and numbers were considered
Simple example:
```php
<?php
  // Validate password strength
  if(strlen($password) < 8){
      echo "Password must be at least 8 characters";
      exit;
  }
    
  if(!preg_match("/[A-Z]/", $password) || !preg_match("/[a-z]/", $password) || !preg_match("/[0-9]/", $password)){
      echo "Password must contain at least one uppercase letter, one lowercase letter, and one number";
      exit;
  }
?>
```
  
#### 3. Prepared statements
Prepared statements with parameterized queries are used to prevent SQL injection attacks.
Simple example:
```php
<?php
      $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
      $stmt->bind_param("sss", $sanitizedName, $sanitizedEmail, $hashedPass);
      $stmt->execute();
      // account creation successful
      $stmt->close();
      return true;
?>  
```

### Login <a name="login"> </a>

Once a user account is create they can login into the system but we need to have security measure to ensure that the person trying to use the specific account is the owner of that account.

To facilitate this, certian security measures were taken to prevent brute force attacks and sql injections.

#### 1. Prepared Statements
This prevents ```SQL injections```. Refer to the account creation to view the code for this.

#### 2. Failed login attempts
Failed login attempts are tracked and stored in the database for each user.

When the count reaches 5 login attempts, the user's account is locked until the situation is resolved.

If the user successfully logs in before the maximum login attempts is reached, the count is reset and they are logged into the system.

This prevents ```Brute force attacks```.
