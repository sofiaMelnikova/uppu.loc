<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../css/myCss.css">
    {% endblock %}
</head>
<body>
<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-right">
            <li><a href="/logout" type="button" class="btn btn-info" ">Logout</a></li>
        </ul>
    </div>
</nav>

<div class="content">
    <table class="table">
        <thead>
        <tr>
            <th>id</th>
            <th>Photo</th>
            <th>Product`s name</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
        </thead>
        <tbody>
        {% for product in products %}
        <tr>
            <th scope="row">{{product.id}}</th>
            <td><img class="photoProduct" src="../{{product.picture}}"></td>
            <td>{{product.product_name}}</td>
            <td><a type="button" class="btn btn-primary" href="/editProduct?id={{product.id}}">Edit</a></td>
            <td><form method="post" action="/deleteProduct"><button type="submit" class="btn btn-danger" name="id" value="{{product.id}}">Delete</button></form></td>
        </tr>
        {% endfor %}
        </tbody>
    </table>

    <nav aria-label="Page navigation example">
        <ul class="pagination">
            {% if pages.min != 1 %}
            <li class="page-item"><a class="page-link" href="http://127.0.0.1/adminGoods/{{pages.min-1}}">Previous</a></li>
            {% endif %}

            {% for page in pages.min..pages.max %}
            <li class="page-item"><a class="page-link" href="http://127.0.0.1/adminGoods/{{page}}">{{page}}</a></li>
            {% endfor %}

            {% if pages.max < sumPages %}
            <li class="page-item"><a class="page-link" href="http://127.0.0.1/adminGoods/{{pages.max+1}}">Next</a></li>
            {% endif %}
        </ul>
    </nav>

    <a href="/addGood?kind=shoes" type="button" class="btn btn-info" ">Add new shoes</a>
    <a href="/addGood?kind=jacket" type="button" class="btn btn-info" ">Add new jacket</a>
    <a href="/addGood?kind=plaid" type="button" class="btn btn-info" ">Add new plaid</a>


    <a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>
</div>


</body>
</html>