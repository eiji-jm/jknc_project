<!DOCTYPE html>
<html>
<head>

<title>UBO System</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

<style>

body{
background:#f5f5f5;
}

.sidebar{
width:250px;
height:100vh;
position:fixed;
background:white;
border-right:1px solid #ddd;
}

.sidebar ul{
list-style:none;
padding:20px;
}

.sidebar li{
padding:8px;
}

.content{
margin-left:260px;
padding:20px;
}

.header{
background:white;
padding:10px;
border-bottom:1px solid #ddd;
}

</style>

</head>

<body>

<div class="sidebar">

<h4 class="p-3">John Kelly & Company</h4>

<ul>
<li>Company General Info</li>
<li>Stock Structure</li>
<li>Board of Directors</li>
<li>Stockholders</li>
<li><b>Ultimate Beneficial Owner</b></li>
<li>Stock and Transfer Book</li>
<li>Corporate Formation</li>
<li>BIR & Tax</li>
<li>Accounting</li>
</ul>

</div>

<div class="content">

<div class="header d-flex justify-content-between">

<form method="GET">
<input type="text" name="search" class="form-control" placeholder="Search">
</form>

<a class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addModal">
+ Contact
</a>

</div>

<div class="mt-4">
@yield('content')
</div>

</div>

</body>
</html>
