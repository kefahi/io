# sedr

Data Management System

The name comes from the Arabic word "سدر" which is the plural for Rhamnus (a tree that grows in the Arabian Peninsula and Levant with dense branches and leaves and deep roots); the singular form is Sedra "سدرة".

In today's world we are sinking in massive amounts of information. Not information that surrounds us but information that we produce or relate to us; your countless photos, videos and files, your contributions to the social media, your emails ...etc. Imagine a system that is capable of consolidating all of that into one with the proper level of abstraction that makes it easy for you to manage your content and interact with with your circles of people providing them controlled access to your data and consuming from them as well; more of an ad-hoc network of p2p content.

Working for so long in using and building data and content centric systems, its clear that regardless of the the specific differences the patterns repeat:
- Individual data entities and their belongings
- Relations among the data entities
- Users with proper access control
- Interface Layer: API with UI on top.

The most profound form of data persistence is files. As plain files are accessible by all consumer apps and can be easily maintained and managed.On the other hand, persisting data in a database engine (being SQL or otherwise) means that you are confined by that engine as your single means of accessing and managing the data; which is not desirable.

So we want to persist the data into files, but we don't want to keep the schema separate from the data. so the data files should also contain all the necessary meta data that allows a piece of code to parse and present to the end consumer.

An entity could actually be a bit more than a single file. it could for example contain images or other rich-media content. so a single entity could end up being in a folder of its own or a single tar-ball / zip file with all its belongings.

Now imagine a wiki page being an entity, a blog article, an email or instant message, a ticket, a spread sheet, an ebook / pdf and so forth an so on.

Sedr plans to abstract all that, and present one software (or better say reference implementation of a software as there is no reason why other compatible software wont be written for it) that would act like all the aforementioned systems and more.

This project aims at providing a unified Data Management System that would deliver the functionality of the systems below:
- Blogger/CMS (e.g. Wrodpress)
- Wiki (e.g. Dokuwiki)
- Ticketing system (e.g. Redmine)
- Instant and offline messages( e.g. XMPP and SMTP/IMAP)
- Dictionary
- Ticketing system
- Invoicing system
- Inventory system
- File management (e.g. OwnCloud)
- Spreadsheet (e.g. Excel)

Looking at all the functions above, its clear that they all revolve around data, meta data and presentation. 

## Principles 
* Data and changes are *almost* immutable (pretty much like git - or based on it - ). This immutability should come at minimal storage overhead (only historic delta's are saved, along with the full most uptodate version)
* Entry-oriented (document oriented): All the related data to an entry (Meta-data, actual content and any file hierarchy) is self contained in the entry object. That is compressed (e.g. entry_abc.tar.bz2). Each entry has a pointer to the respective module (including its specific version requirements) that has the format specifications and can interact with the entry.
* Graph-enabled. Entries can have pointers (relations + attributes) to each other.
* Modular data handlers: Modules that can ingest various types of data: Text, PDF, eBook, Media (Images, Audio, Video), JSON, CSV, Xml, Doc, Excel, Presentation, Avro, ORCFile, Parquet, Sqlite3 db ... etc. A Data handler module also includes the necessary code to parse, enrich and expose data/meta data. Each module would have ...
  * Indexer / Analyzer
  * Schema definition
  * Reader/Writer/Converter
  * Presentation Layer
  * Binaries / Logic / code
* Data entries are indexed so they can be searched. (using Lucene?)
* Its all JVM-base initially.
* Security: Proper access management and an entry is signed by its author
* API Interface that encompasses all the features. High-performance Non-blocking. (netty?)
* UI that interacts with the API; Views (list/Gride/cards/tree/graph), search interface, CRUD. 
* Distributed and decentralized. Just like/on top of your regular file/email/cms systems.
* Does not rely on a Database engine; but rather has the data in textual representation (e.g. JSON)
* Miners are additional components responsible for two things: finding new content and improving the quality/meta information of it. They mine local and external sedras looking for new content and new perspective on already existing content.

## Technologies
Nothing is determined yet. Going now though a research phase.

* [Git](https://github.com/kbjr/Git.php)
* [.tar.bz2](http://php.net/manual/en/class.phardata.php)
* JSON/ [Avro](http://apache.osuosl.org/avro/stable/php/) for schema definitions
* Php
* [Admin LTE](https://github.com/dmstr/yii2-adminlte-asset)/Angular/Metis/SB Admin/Webix
* [Yii2](http://www.yiiframework.com/)
* [AuthClient \](https://github.com/yiisoft/yii2-authclient)

## Folder structure
* *Indexes*: A means of speed access (e.g. RDBMS, NoSQL, Lucene index ...etc). This remains rather an artifact of the master data persisted on the file system. As such it must be always possible to rebuild the indexes without any loss of information.
* *Data* The actual repository holding the data entities along with sub-content.
* *Data revision history*: The deltas of the entities as they came to be
* *Meta data* (Lineage, schema, History): The story of the data and how they became to be.
* *Modules* (code/binaries): Individual modules that can manage (read/write/present) certain types of entities.
```
.
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
├── README.md
└── revisions
```
