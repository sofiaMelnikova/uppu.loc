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
    <form enctype="multipart/form-data" action="/saveChangeProduct" method="post">
        <input type="hidden" name="stokeId" value="{{product.id}}">

        <div class="photoProduct"><img src="{{product.picture}}"></div>

        <div class="form-group">
            <label for="exampleFormControlFile1">Choose new photo</label>
            <input name="photo" type="file" class="form-control-file" id="exampleFormControlFile1">
        </div>

        <div class="form-group">
            <label for="exampleFormControlSelect1">Kind</label>
            <select name="kind" class="form-control" id="exampleFormControlSelect1">
                <option value="shoes" {% if product.kinds_value == 'shoes' %} selected {% else %} disabled {% endif %}>Shoes</option>
                <option value="jacket" {% if product.kinds_value == 'jacket' %} selected {% else %} disabled {% endif %}>Jacket</option>
                <option value="plaid" {% if product.kinds_value == 'plaid' %} selected {% else %} disabled {% endif %}>Plaid</option>
            </select>
        </div>

        <div class="form-group">
            <label for="exampleFormControlInput1">Product`s name</label>
            <input class="form-control" id="exampleFormControlInput1" name="productName" placeholder="Enter product`s name" value="{{product.product_name}}">
        </div>

        {% if product.brand %}
        <div class="form-group">
            <label>Brand</label>
            <input class="form-control" name="brand" placeholder="Enter brand" value={{ product.brand }}>
        </div>
        {% endif %}

        {% if product.color %}
        <div class="form-group">
            <label for="exampleFormControlInput1">Color</label>
            <input class="form-control" id="exampleFormControlInput1" name="color" placeholder="Enter color" value="{{product.color}}">
        </div>
        {% endif %}

        {% if product.size %}
        <div class="form-group">
            <label for="exampleFormControlInput1">Size</label>
            <input class="form-control" id="exampleFormControlInput1" name="size" placeholder="Enter size" value="{{product.size}}">
        </div>
        {% endif %}

        {% if product.length %}
        <div class="form-group">
            <label>Length</label>
            <input class="form-control" name="length" placeholder="Enter length" value={{ product.length }}>
        </div>
        {% endif %}

        {% if product.width %}
        <div class="form-group">
            <label>Width</label>
            <input class="form-control" name="width" placeholder="Enter width" value={{ product.width }}>
        </div>
        {% endif %}

        {% if product.material %}
        <div class="form-group">
            <label for="exampleFormControlInput1">Material</label>
            <input class="form-control" id="exampleFormControlInput1" name="material" placeholder="Enter material" value="{{product.material}}">
        </div>
        {% endif %}

        {% if product.gender %}
        <div class="form-group">
            <label for="exampleFormControlSelect1">Gender</label>
            <select name="gender" class="form-control" id="exampleFormControlSelect1">
                <option></option>
                <option value="man" {% if product.gender == 'man' %} selected {% endif %}>man</option>
                <option value="woman" {% if product.gender == 'woman' %} selected {% endif %}>woman</option>
            </select>
        </div>
        {% endif %}

        {% if product.producer %}
        <div class="form-group">
            <label>Made in</label>
            <input class="form-control" name="producer" placeholder="Enter producer" value={{ product.producer }}>
        </div>
        {% endif %}

        <div class="form-group">
            <label for="exampleFormControlInput1">Count</label>
            <input class="form-control" id="exampleFormControlInput1" name="count" placeholder="Enter count" value="{{product.count}}">
        </div>

        <div class="form-group">
            <label for="exampleFormControlInput1">Cost</label>
            <input class="form-control" id="exampleFormControlInput1" name="cost" placeholder="Enter cost" value="{{product.cost}}">
        </div>

        <button type="submit" class="btn btn-success">Save change</button>
    </form>

    <form action="/deleteProduct" method="post" class="toInlineBlock">
        <button type="submit" class="btn btn-danger" name="stokeId" value="{{product.id}}">Delete</button>
    </form>

    <a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
    <a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>

    {{error}}
</body>
</html>