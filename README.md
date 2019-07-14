# Laravel elasticsearch model (Beta version)

## Installation

- [Introduction](#introduction)
- [Installation](#installation)
- [Examples](#examples)
    - [Mapping](#mapping)
    

<a name="introduction"></a>
## Introduction

Laravel Elasticsearch Model package provides an easy way to search throw the Elasticsearch documents by
using build in methods. 

<a name="installation"></a>
## Installation
To use the search methods use the searchable trait which is located `namespace Draotix\LaravelElasticsearch\Traits;`. 

    use Searchable; 
    
To use the mapping in the model the `SearchableInterface` should be implemented and the `Searchable` trait should be called.
`SearchableInterface` will make you implement the `mapUp` and `mapDown` methods. 

The `mapUp` method is where the field mapping should be done. Call `php artisan elasticMap:up` on the terminal to do the mapping. Example: 

    public static function mapUp()
    {
        // TODO: Implement mapUp() method.
    }

The `mapDown` method is used to delete the mapping:

    public static function mapDown()
    {
        self::map()->delete();
    }


<a name="examples"></a>
## Examples

<a name="mapping"></a>
### Mapping

    $map
        ->integerRange("integerRange_test")
        ->dateRange("dateRange_test")
        ->boolean("boolean_test")
        ->date("date_test")
        ->keyword("keyword_test")
        ->nested("nested_test", function (IndexBuilder $mapper) {
            return $mapper->integer("nested1")
                ->integer("nested2")
                ->integer("nested3");
        })
        ->long("long_test")
        ->object("object_test", function ($mapper) {
            return $mapper->integer("object1")
                ->integer("object2")
                ->integer("object3");
        })
        ->text("title_integer")
        ->join("join_test", "thhis_is_the_parenet", "child")
        ->build();
        
<a name="search"></a>
### Search


#### Get all

    $response = Article::search()
            ->all();

#### Match 

    $response = Article::search()
        ->match("title", "a")
        ->get();

#### Multi match        
    $response = Article::search()
        ->multiMatch("foo", ["title", "slug"])
        ->get();

#### Term

    $data = Article::search()
        ->term("state", "Pa")
        ->get();
        
#### Range
    
    $data = Article::search()
            ->range("age", ["gte" => 1, "lte" => 30])
            ->get();
