<?php

require_once("../../config.php");

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="../../assets/style.css">
</head>
<body>
    <header>
        <nav class="navigation">
            <ul class="nav navbar-nav">
                <li class="active">
                    <a href="">Overview</a>
                </li>
                <li>
                    <a href="">Expenses</a>
                </li>
                <li>
                    <a href="">Income</a>
                </li>
                <li>
                    <a href="">Budget</a>
                </li>
            </ul>
        </nav>
        <div class="header-right">
            <div class="dropdown hidden-sm hidden-xs">
                <button class="btn btn-primary dropdown-toggle" type="button" data-toggle="dropdown">
                    <span>
                        <i class="mdi mdi-plus-circle-outline"></i>
                    </span>
                    New record
                </button>
                <ul class="dropdown-menu">
                    <li role="presentation">
                        <a href="" role="menuitem" data-toggle="modal" data-target="#addExpense">
                            <i class="mdi mdi-chevron-right"></i>
                            Expense
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="" role="menuitem" data-toggle="modal" data-target="#addIncome">
                            <i class="mdi mdi-chevron-right"></i>
                            Income
                        </a>
                    </li>
                </ul>
            </div>
            <div class="dropdown">
                <span class="dropdown-toggle" data-toggle="dropdown" aria-expanded="false">
                    <span class="avatar">
                        <img src="" class="img-circle">
                    </span>
                    <span class="profile-name">
                        <span class="hidden-xs">Elvis Mutinda</span>
                        <i class="mdi mdi-menu-down-outline"></i>
                    </span>
                </span>
                <ul class="dropdown-menu profile-name" role="menu" aria-labelledby="menu1">
                    <li role="presentation">
                        <a href="" role="menuitem">
                            <i class="mdi mdi-account-multiple"></i>
                            Users
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="" role="menuitem">
                            <i class="mdi mdi-settings"></i>
                            Settings
                        </a>
                    </li>
                    <li role="presentation">
                        <a href="" role="menuitem">
                            <i class="mdi mdi-logout"></i>
                            Logout
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </header>
    <div class="container">
        <div class="page-heading">
            <a href="" class="btn btn-default pull-right ml-5">
                <span>
                    <i class="mdi mdi-adjust"></i>
                </span>
                Check Budget
            </a>
            <div class="heading-content">
                <div class="heading-title">
                    <h2>Welcome back, Elvis Mutinda</h2>
                    <p>This is your dashboard. It gives an overview of everything.</p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>