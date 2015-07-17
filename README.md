## Sortable Relations for Bolt 

### Experimental warning - read details below

This extension allows you to mark relations as being sortable. The sort order is persisted to the database.

You can only use this on relations that are marked as: `multiple: true`

For the example as used in the screenshots, here's the relevant entry in `contenttypes.yml`.

    pages:
        fields:
            ....
        relations:
            pages:
              multiple: true
              sortable: true
              label: Select related pages
            
The important addition is the `sortable: true` after adding this your relations they will become sortable.



### Experimental Warning

This extension has not yet been tested by a large audience, and as such it may carry a few risks. 

Firstly the extension will modify one of Bolt's core database tables. Should you uninstall the extension the extra database columns will be removed and any sorting data will be lost. Since the extension is working on a core database table there are fewer protections against data loss implemented. Keep backups of important data during usage.

Secondly, relations in Bolt are not necessarily one-way, at present, this extension is only able to handle sorting in one direction and so in cases where you enable `sortable` on both directions of the join, you may get unexpected results.