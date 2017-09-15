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
<body>
    <div class="bs-example" data-example-id="contextual-panels">
        <div><img class="photoProduct" src="{{product.picture}}"></div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Product`s name</h3>
            </div>
            <div class="panel-body">{{product.product_name}}</div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Kind</h3>
            </div>
            <div class="panel-body">{{product.kinds_value}}</div>
        </div>

        {% if product.gender %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Gender</h3>
            </div>
            <div class="panel-body">{{product.gender}}</div>
        </div>
        {% endif %}

        {% if product.brand %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Gender</h3>
            </div>
            <div class="panel-body">{{product.brand}}</div>
        </div>
        {% endif %}

        {% if product.color %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Color</h3>
            </div>
            <div class="panel-body">{{product.color}}</div>
        </div>
        {% endif %}

        {% if product.material %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Material</h3>
            </div>
            <div class="panel-body">{{product.material}}</div>
        </div>
        {% endif %}

        {% if product.length %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Material</h3>
            </div>
            <div class="panel-body">{{product.material}}</div>
        </div>
        {% endif %}

        {% if product.width %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Material</h3>
            </div>
            <div class="panel-body">{{product.material}}</div>
        </div>
        {% endif %}

        {% if product.size %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Size</h3>
            </div>
            <div class="panel-body">{{product.size}}</div>
        </div>
        {% endif %}

        {% if product.producer %}
        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Size</h3>
            </div>
            <div class="panel-body">{{product.producer}}</div>
        </div>
        {% endif %}

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Count</h3>
            </div>
            <div class="panel-body">{{product.count}}</div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">
                <h3 class="panel-title">Price</h3>
            </div>
            <div class="panel-body">{{product.cost}}</div>
        </div>
        <a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>
    </div>
</body>
</html>