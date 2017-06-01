#########################################################
#Taylor Webber, June 2015                               #
#Update Is_On_Amazon in HP with what is listed on Amazon#
#########################################################

import pyodbc
import csv
import time

def open_odbc_connection(DSN,UID,PWD):
    return pyodbc.connect('DSN='+DSN+';UID='+UID+';PWD='+PWD+';')
    
def run_sql_statement(ODBC_CONN,SQL,SQL_VAR_1):
    #set cursor for connection
    CUR_OBJ = ODBC_CONN.cursor()
    print 'cursor created'
    #execute SQL statement
    CUR_OBJ.execute(SQL,SQL_VAR_1)
    print 'update ran'
    ODBC_CONN.commit()
    print 'committed'
    
    #return result


# Change the stuff below this text
PLC_CODE = 'XXX'         # PLC we are checking
PART_NUMBER = '123456'   # Part Number we are checking
CSV_TO_READ = 'setRecordFalse.csv'  # This assumes your part numbers are in the first column
                         # Also your parts have the PLC and Part Number
                         # ie. LND EX-0122-07.
CSV_TO_READ_DEL = '\t'   # The delimiter on the CSV you are reading
ODBC_CONN_NAME = 'HPCommerce'         # The ODBC connection name
ODBC_USER = ''                        # The ODBC user name, '' if there is no user required
ODBC_PASS = ''                        # The ODBC pass, '' if there is no password required
# Change the stuff above this text


#Open Database Connection
ODBC_CONN = open_odbc_connection(ODBC_CONN_NAME,ODBC_USER,ODBC_PASS)
#Open CSV file
csv_RecordActive = csv.reader(open(CSV_TO_READ), delimiter=CSV_TO_READ_DEL)
#run through CSV by row
for row in csv_RecordActive:
    #split sku into PLC, PartNumber
    SKU = row[0]
    print SKU
    #make call to database
    #run_sql_statement(ODBC_CONN, "UPDATE Imaster SET Is_On_Amazon = TRUE FROM Imaster INNER JOIN Plc ON Plc.PLCID = Imaster.PLCID WHERE Plc.PLC = ? AND Imaster.PartNumber = ?", [PLC_CODE], [PART_NUMBER])
    run_sql_statement(ODBC_CONN,"UPDATE Imaster SET RecordActive = FALSE FROM Imaster INNER JOIN Plc ON Plc.PLCID = Imaster.PLCID WHERE PLC.PLC + ' ' + Imaster.PartNumber = ?",[SKU])
    

print 'This script executed successfully'
print 'This script will close in 5 seconds'
time.sleep(1)
print '1..'
time.sleep(1)
print '2..'
time.sleep(1)
print '3..'
time.sleep(1)
print '4..'
time.sleep(1)
print '5..'

