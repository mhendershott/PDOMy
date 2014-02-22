PDOMy
=====

helper class for common PHP MySQL PDO tasks

Where functions ask for $data (addRow, updateRow) the structure is:

$data = array (
  columnName=>value,
  columnName2=>value2
  );
  
On updateRow() only the columns you are updating need to be included.

When using addRow() all columns defined as 'not null' are required.


  
