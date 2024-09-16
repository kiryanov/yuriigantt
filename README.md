# Yurii's Gantt Plugin

This plugin allows you to add a Gantt table into a Dokuwiki page. The version in this repository is modified from the original, in particular it disables most of the drag-and-drop functionality and forces a specific sort order.

![alt text](docs/img/raw.png "Rendered")
![alt text](docs/img/rendered.png "Rendered")

In a nutshell, it just wraps [dhtmlxGantt](https://dhtmlx.com/docs/products/dhtmlxGantt).
To use dhtmlxGantt in non-GPL projects (and get Pro version of the product), please obtain Commercial/Enterprise or Ultimate license on their site https://dhtmlx.com/docs/products/dhtmlxGantt/#licensing or contact them at sales@dhtmlx.com
Replace installation code at *lib/plugins/yuriigantt/3rd/dhtmlxgantt* with your dhtmlxGantt PRO.

P.S.: This plugin is shipped with dhtmlxGantt Standard v.8.0.10

## Installation

1. Automatic
    ```
    TODO
    ```
1. Manual
    ```
    cd YOUR_DOKUWIKI_DIR/lib/plugins
    git clone --single-branch --branch master https://github.com/kiryanov/yuriigantt.git yuriigantt
    ```

## Usage

1. Create a new dokuwiki page in your browser
1. Add the least required syntax into the page
    ```
    ~~~~GANTT~~~~

    ~~~~~~~~~~~
    ```
1. Save. You must see now ![alt text](docs/img/rendered_empty.png "Rendered")
1. (Optional) Click page edit. You will see that the data for an empty embedded database was initialized
    ```
    ~~~~GANTT~~~~
    {
        "pageId": "asd",
        "version": "1.0",
        "dsn": ":embedded:",
        "increment": {
            "task": 1,
            "link": 1
        },
        "gantt": {
            "data": [],
            "links": []
        }
    }
    ~~~~~~~~~~~
    ```
You can also try [this demo example](_test/test_page.txt) instead.

## How It Works

NOTE! Currently only embedded database driver is supported

![alt text](docs/img/diagram.png "Diagram")

### Drivers
#### Embedded
Gantt database is stored in a page within special pattern in JSON format.
```
~~~~GANTT~~~~
{
    "pageId": "asd", <--- page identifier
    "version": "1.0", <-- RESERVED version idetificator
    "dsn": ":embedded:", <-- says parser what database driver was used
    "increment": { <--- EMBEDDED table increments
        "task": 12,
        "link": 8
    },
    "gantt": { <----- EMBEDDED table data
~~~~~~~~~~~
```
On each user update dokuwiki page is parsed, database extracted, its data gets changed and with new changes database is stored back to the dokuwiki page.


**NOTE! If you have a database larger than 10 kilobytes please consider using other drivers!**

## For Developer

If you change syntax parser, to refresh rendered page cache please run page with purge
```
http://127.0.0.1:8000/doku.php?id={PAGEID}&purge=true
```

#### DXHTML

* https://docs.dhtmlx.com/gantt/samples
