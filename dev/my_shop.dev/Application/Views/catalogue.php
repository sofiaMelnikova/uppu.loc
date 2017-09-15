<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Title</title>
    {% block stylesheets %}
    <link rel="stylesheet" type="text/css" href="../../css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="../../css/myCss.css">
    {% endblock %}
</head>
<body>

<nav class="navbar navbar-default">
    <div class="container-fluid">
        <ul class="nav navbar-nav navbar-left">
            <li>
                <a class=" active" href="/catalogue/shoes/1">Shoes</a>
            </li>
            <li>
                <a href="/catalogue/jacket/1">Jacket</a>
            </li>
            <li>
                <a href="/catalogue/plaid/1">Plaid</a>
            </li>

        </ul>
        <ul class="nav navbar-nav navbar-right">
            {% if login %}
            <li><a href="/showBasket">Basket <span class="badge">{% if countProductsInBasket %}{{countProductsInBasket}}{% else %}0{% endif %}</span></a></li>
            <li><a href="/logout" type="button" class="btn btn-info" ">Logout: {{login}}</a></li>
            {% else %}
            <li><a href="/showBasket">Basket <span class="badge">{% if countProductsInBasket %}{{countProductsInBasket}}{% else %}0{% endif %}</span></a></li>
            <li><a href="/login" type="button" class="btn btn-info" ">Login</a></li>
            {% endif %}
        </ul>
    </div>
</nav>


<div class="card-deck">
    {% for product in products %}
    <div class="card">
        <img class="card-img-top" src="../../{{product.picture}}" alt="Card image cap">
        <div class="card-body">
            <h4 class="card-title">{{product.cost}}</h4>
            <a href="/product?id={{product.id}}" type="button" class="btn btn-primary" class="card-link">{{product.product_name}}</a>
            <a href="/takeToTheBasket?id={{product.id}}" type="button" class="btn btn-success" class="card-link">Add to the basket</a>
        </div>
    </div>
    {% endfor %}
</div>



<nav aria-label="Page navigation example">
    <ul class="pagination">
        {% if pages.min != 1 %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{pages.min-1}}">Previous</a></li>
        {% endif %}

        {% for page in pages.min..pages.max %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{page}}">{{page}}</a></li>
        {% endfor %}

        {% if pages.max < sumPages %}
        <li class="page-item"><a class="page-link" href="http://127.0.0.1/catalogue/{{kind}}/{{pages.max+1}}">Next</a></li>
        {% endif %}
    </ul>
</nav>

{% if admin %}
    <a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
{% endif %}
</body>
</html>