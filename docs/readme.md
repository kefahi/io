## Design

This is essentially a distributed data management system.

 *Below is a high-level depiction*

![High-level block digram](https://rawgit.com/kefahi/io/master/docs/design.png)

## Principles 
* Data and changes are *almost* immutable (pretty much like git - or based on it - ). This immutability should come at minimal storage overhead (only historic delta's are saved, along with the full most up-to-date version)
* Entry-oriented (document oriented): All the related data to an entry (Meta-data, actual content and any file hierarchy) is self contained in the entry object. That is compressed (e.g. entry_abc.tar.bz2). Each entry has a pointer to the respective module (including its specific version requirements) that has the format specifications and can interact with the entry.
* Graph-enabled. Entries can have pointers (relations + attributes) to each other.
* Modular data handlers: Modules that can ingest various types of data: Text, PDF, eBook, Media (Images, Audio, Video), JSON, CSV, Xml, Doc, Excel, Presentation, Avro, ORCFile, Parquet, Sqlite3 db ... etc. A Data handler module also includes the necessary code to parse, enrich and expose data/meta data. 
* Data entries are indexed so they can be searched. (using indexing technologies such as lucene)
* Security: Proper access management and an entry is signed by its author
* API Interface that encompasses all the features. High-performance Non-blocking. (netty?)
* UI that interacts with the API; Views (list/Gride/cards/tree/graph), search interface, CRUD. 
* Distributed and decentralized. Just like/on top of your regular file/email/cms systems.
* Does not rely on a Database engine; but rather has the data in textual representation (e.g. JSON)
* Miners are additional components responsible for two things: finding new content and improving the quality/meta information of it. They mine local and external sedras looking for new content and new perspective on already existing content.

## Content Modules

Each module would have:
  * Indexer / Analyzer
  * Schema definitions
  * Reader/Writer/Converter
  * Presentation Layer
  * Binaries / Logic / code
  
## Miners
Miners search for content and people according to the user's rules/criteria. e.g. Get me my friends updates that are of certain topics (tags), deliver messages. They maintain the meta data from all connected instances and from other miners.

### Public Miner
* Push: events (distribute to registered inboxes and meta index). a registered inbox receives direct messages and also registeres rules for interesting events. every event/entity is signed by its author. encryption is optional.
circls: of people (actually a social graph) along with classification (friends, acquaintance, family ...etc)
* Poll/crawl: to meta index. A meta index keeps reference to entities.
* app-store for modules (with all historic versions, so content referring to a specific version can get that respective version)

### Other considerations

* Realtime-ness. TBD
* Events chaining (consequetive events where order need to be maintained). comments/concurrent document editing ... etc.
* key/trust-management? should we use existing pgp?
* identity managment is handled by oauth and friends.

## Folder structure

* **data**: The Data repository, it holds all data entities and belongings/files. The way folders are arranged under this is left to the user.
* **meta**: The story of the data and how it became to be
* **modules**: aka code/binaries: individual modules that can manage (read/write/present) certain types of entities.
* **events**: aka messages / changes: The individual change events that would contribute to the data repository.
* **revisions**: aka attic. The old delta copies of the data. It should be possible to delete or compact this.
* **indexes**: Indexes for data and metadata to make finding, querying data faster. This can be regenerated at any time. it could be (RDBMS like sqlite, NoSQL, Lucene indexes ..etc).

```
sample
├── data
│   ├── family
│   ├── files
│   │   ├── books
│   │   ├── documents
│   │   └── media
│   ├── friends
│   ├── interests
│   ├── links
│   ├── messages
│   ├── personal
│   └── structures
│       ├── financial
│       │   └── invoices
│       ├── inventory
│       ├── pages
│       └── tickets
├── events
├── indexes
├── meta
│   ├── history
│   ├── lineage
│   └── schema
├── modules
├── people
└── revisions
```

## ERD

* Changes and EntityProperty are part-of Entity
* LinkProperty is part-of Link

In a NoSQL document oriented setup; there would be two or three main schema.

![ERD (for RDBMS)](https://rawgit.com/kefahi/io/master/docs/erd.png)

### Entity Types -> Sub-types
* Actor
  * Person
  * Group
* Content
  * Plain
  * Rich
  * Structured
  * Message
  * Wiki
  * Binary
* Container
  * Folder
  * Tarball/zip

### Datum Types
* Embedded
* External

Datum formats vary, here are few examples: 
JSON, XML, Avro, Markdown, Xhtml, Images, Audio, Video, PDF, Documents, Binary, Sqlitedb

### Change Types
* Create
* Update

## Implementation Technologies
Nothing is determined yet. Going now though a research phase.

Edraj can be implemented in a number of technology combinations:
* C++/QT fro Native cross-platfrom desktop/mobile app
* Java/Scala
* Php/yii2
* NodeJS/IO.JS

At this point wer are considering Php/Yii2.

* [Git](https://github.com/kbjr/Git.php)
* [.tar.bz2](http://php.net/manual/en/class.phardata.php)
* JSON/ [Avro](http://apache.osuosl.org/avro/stable/php/) for schema definitions
* Php
* [Admin LTE](https://github.com/dmstr/yii2-adminlte-asset)/Angular/Metis/SB Admin/Webix
* [Yii2](http://www.yiiframework.com/)
* [AuthClient \](https://github.com/yiisoft/yii2-authclient)
