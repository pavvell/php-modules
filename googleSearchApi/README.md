## googleSearchApi PHP Class

With this class you can implement search queries using google search engine. This class is based on [Google Custom Search Api](https://developers.google.com/custom-search/).

## Requirements

You need to get `API key` and `Custom search engine ID`, in order to use this class. Learn more about how to get them on [official google page](https://developers.google.com/custom-search/).

## Setup

Just download this class and place it in a directory where your other classes are.

## API Guide

First of all, you need to create an instance, and then, you can send queries:
```php
$g = new googleSearchApi('API_KEY', 'SEARCH_ID');
```

Simple query. Here we chain two methods: `query()` and `get()`. The result is gonna be an array with 10 found items or an empty array if nohing is found:
```php
$items = $g->query('Mediterranean Sea Hotels')->get();
```

Image Search:
```php
$items = $g->query('Mediterranean Sea')->imagesOnly()->get();
```

Shorthanded method for image search:
```php
$items = $g->searchImages('Mediterranean Sea');
```

Total results:
```php
$items = $g->searchImages('Mediterranean Sea');
echo $g->total(); // this will print number like: 7430000
```

Another way to get result items:
```php
$g->searchImages('Mediterranean Sea');
$total = $g->total();
$items = $g->items();
```

Getting raw google response:
```php
$g->query('Mediterranean Sea Hotels')->get();
$rawResponse = $g->raw();
echo var_dump( $rawResponse );
```

## Query configurations

You can configure queries by chaining this methods to the `query()` method like this:

```php
$g->query('Mediterranean Sea')
    ->safeSearch('medium')
    ->imagesOnly()
    ->country('IT')
    ->rawOptions( ['imgType' => 'photo', 'imgSize' => 'huge'] )
    ->page(2)
    ->get();

echo 'Total: ' . $g->total();
echo 'Results: ';
echo var_dump($results);
```

- `safeSearch(mode)`, accepted values for `mode`: `off`, `medium`, `high`
- `imagesOnly()`, results will be containing only images
- `country(code)`, code must be 2 letters country code like: 'NL', 'US', 'RU' etc. By specifing country code your're telling google to search among web sites of specified country.
- `page(num)`, `num` - must be an integer, and specifies a number of search result's page you want to get. The result will be containing 10 items (web sites/images) from this page.
- `rawOptions(options)`, options must be an associative array (optionName => Value). All available options for configuring google search queries available [here](https://developers.google.com/custom-search/json-api/v1/reference/cse/list). All methods listed above is just wrappers of `rawOptions()` and are made for readability.