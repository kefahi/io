# sedr

Data Management System

The name comes from the Arabic word "سدر" which is the plural for Rhamnus (a tree that grows in the desert with massive leaf-pattern); the singular form is Sedra "سدرة".

This project aims at providing a unified Data Management System that would deliver the functionalities of the systems below:
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
* Entry-oriented (document oriented): All the related data to an entry (Meta-data, actual content and any file hierarchy) is self contained in the entry object. That is comprssed (e.g. entry_abc.tar.bz2). Each entry has a pointer to the respective module (including its specific version requirements) that has the format specifications and can interact with the entry.
* Graph-enabled. Entries can have pointers (relations + attributes) to each other.
* Modular data handlers: Modules that can ingest various types of data: Text, PDF, eBook, Media (Images, Audio, Video), JSON, CSV, Xml, Doc, Excel, Presentation, Avro, ORCFile, Parquet, Sqlite3 db ... etc. A Data handler module also includes the necessary code to parse, enrich and expose data/meta data. Each module would have ...
  * Indexer / Analyzer
  * Schema definition
  * Reader/Writer/Convertor
  * Presentation Layer
  * Binaris / Logic / code
* Data entries are indexed so they can be searched. (using Lucene?)
* Its all JVM-base initially.
* Security: Proper access management and an entry is signed by its author
* API Interface that encompasses all the features. High-performance Non-blocking. (netty?)
* UI that interacts with the API; Views (list/Gride/cards/tree/graph), search interface, CRUD. 
* Distriuted and decentralized. Just like/on top of your regular file/email/cms systems.
* Does not rely on a Database engine; but rather has the data in textual representation (e.g. JSON)
* Miners are additional components responsible for two things: finding new content and improving the quality/meta information of it. They mine local and external sedras looking for new content and new perspective on already existing content.

## Technologies
Nothing is determined yet. Going now though a research phase.

* Git
* .tar.bz2
* JSON/Avro for schema definitions
* Php
* Admin LTE/Angular/Metis/SB Admin/Webix
* Yii2


## File structure
* Indexes section (temprary indexes generated on the fly)
* Data repository 
* Data revision history
* Meta data (Lineage, schema, History)
* Modules (code/binaries)
