<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="css/myCss.css">
    {% endblock %}
</head>
<body class="content">
    <table class="table">
        <thead>
        <tr>
            <th>#</th>
            <th>Photo</th>
            <th>Product`s name</th>
            <th>Cost</th>
            <th>Count</th>
            <th>Sum</th>
            <th>Data</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            {% for product in products %}
            <th scope="row">1</th>
            <td><img class="photoProduct" src="/{{product.picture}}"></td>
            <td>{{product.product_name}}</td>
            <td>{{product.cost}}</td>
            <td>{{product.count}}</td>
            <td>{{product.summ_cost}}</td>
            <td>{{product.date}}</td>
            {% endfor %}
        </tr>
        </tbody>
    </table>
</body>
</html>