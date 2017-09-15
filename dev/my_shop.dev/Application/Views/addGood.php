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
<form enctype="multipart/form-data" action="http://127.0.0.1/addGood" method="post">
    <div><img src="pictures/addPhoto.png"></div>
    <input name="photo" type="file" />
    <div class="col-auto">
        <label class="mr-sm-2" for="inlineFormCustomSelect">Kind</label>
        <select name="kind" class="form-control" id="exampleFormControlSelect1">
            <option value="shoes" {% if properties.kind == 'shoes' %} selected {% else %} disabled {% endif %}>Shoes</option>
            <option value="jacket" {% if properties.kind == 'jacket' %} selected {% else %} disabled {% endif %}>Jacket</option>
            <option value="plaid" {% if properties.kind == 'plaid' %} selected {% else %} disabled {% endif %}>Plaid</option>
        </select>
    </div>

    <div class="form-group">
        <label>Product`s name</label>
        <input class="form-control" name="productName" placeholder="Enter product`s name" value={{ product.productName }}>
    </div>

    {% if properties.brand %}
    <div class="form-group">
        <label>Brand</label>
        <input class="form-control" name="brand" placeholder="Enter brand" value={{ product.brand }}>
    </div>
    {% endif %}

    {% if properties.color %}
    <div class="form-group">
        <label>Color</label>
        <input class="form-control" name="color" placeholder="Enter color" value= {% if product.color %} {{ product.color }} null {% endif %}>
    </div>
    {% endif %}

    {% if properties.size %}
    <div class="form-group">
        <label>Size</label>
        <input class="form-control" type="number" min="{{properties.size.min}}" max="{{properties.size.max}}" name="size" placeholder="Enter size" value={{ product.size }}>
    </div>
    {% endif %}

    {% if properties.length %}
    <div class="form-group">
        <label>Length</label>
        <input class="form-control" name="length" placeholder="Enter length" value={{ product.length }}>
    </div>
    {% endif %}

    {% if properties.width %}
    <div class="form-group">
        <label>Width</label>
        <input class="form-control" name="width" placeholder="Enter width" value={{ product.width }}>
    </div>
    {% endif %}

    {% if properties.material %}
    <div class="form-group">
        <label>Material</label>
        <input class="form-control" name="material" placeholder="Enter material" value={{ product.material }}>
    </div>
    {% endif %}

    {% if properties.gender %}
    <div class="col-auto">
        <label class="mr-sm-2" for="inlineFormCustomSelect">Gender</label>
        <select name="gender" class="form-control" id="exampleFormControlSelect1">
            <option></option>
            <option value="man" {% if product.gender == 'man' %} selected {% endif %}>man</option>
            <option value="woman" {% if product.gender == 'woman' %} selected {% endif %}>woman</option>
        </select>
    </div>
    {% endif %}

    {% if properties.producer %}
    <div class="form-group">
        <label>Made in</label>
        <input class="form-control" name="producer" placeholder="Enter producer" value={{ product.producer }}>
    </div>
    {% endif %}

    <div class="form-group">
        <label>Count</label>
        <input class="form-control" name="count" placeholder="Enter count" value={{ product.count }}>
    </div>

    <div class="form-group">
        <label>Cost</label>
        <input class="form-control" name="cost" placeholder="Enter cost" value={{ product.cost }}>
    </div>

    <button type="submit" class="btn btn-primary">Submit</button>
</form>

<a href="/adminGoods" type="button" class="btn btn-primary">For admin goods list</a>
<a href="/catalogue" type="button" class="btn btn-primary">For uer catalog</a>
{{error}}

</body>
</html>