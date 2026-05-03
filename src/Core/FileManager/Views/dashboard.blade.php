<!DOCTYPE html>
<html>
<head>
    <title>FileManager</title>
</head>
<body>

<h2>Dashboard</h2>

<p>Welcome: {{ $_SESSION['btv_user'] ?? 'guest' }}</p>

<form method="POST" action="/btv/scy/filemanager/delete">
    <input name="file" placeholder="file path">
    <button>Delete</button>
</form>

</body>
</html>