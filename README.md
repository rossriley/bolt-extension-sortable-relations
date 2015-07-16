## Sortable Relations for Bolt 

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