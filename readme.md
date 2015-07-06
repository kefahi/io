# edraj

Data Management System

The name **edraj** (in Arabic "إدراج") comes from the merge of two Arabic words "إدخال" (input) and "إخراج" (output) to reflect the nature of this system that deals with ingesting data and surfacing it up. The merged Arabic word edraj also means enroll.

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

edraj plans to abstract all that, and present one software (or better say reference implementation of a software as there is no reason why other compatible software wont be written for it) that would act like all the aforementioned systems and more.

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

Check the [Design](docs/readme.md) page for more technical details.

