import pyodbc
import csv
import time

def open_odbc_connection(DSN,UID,PWD):
    return pyodbc.connect('DSN='+DSN+';UID='+UID+';PWD='+PWD+';')

def make_csv_file(CSV_NAME,DEL):
    return csv.writer(open(CSV_NAME, "wb"),delimiter=DEL)
    
def make_SQL_call(ODBC_CONN,SQL,SQL_VAR):
    CUR_OBJ = ODBC_CONN.cursor()
    result = CUR_OBJ.execute(SQL,SQL_VAR)
    results_array = []
    make = []
    model = []
    year = []
    trim = []
    engine = []
    counter = 0
    for x in result: # If selecting more than 1 thing print x and look
        if 'Make' == x[0]:
            make.append(x[1])
            #print("added make")
            print(x[1])
            counter+=1
        elif 'Model' == x[0]:
            model.append(x[1])
            #print("added model")
            print(x[1])
            counter+=1
        elif 'Year' == x[0]:
            year.append(x[1])
            #print("added year")
            print(x[1])
            counter+=1
        elif 'Trim' == x[0]:
            trim.append(x[1])
            #print("added trim")
            print(x[1])
            counter+=1
        elif 'Engine' == x[0]:
            engine.append(x[1])
            #print("added engine")
            print(x[1])
            counter = 0
        
    return make,model,year,trim,engine



# Change the stuff below this text
PLC_CODE = 'AVS'                            # PLC we are checking
CSV_TO_READ = 'SKUs_without_Fitments.csv'   # This assumes your part numbers are in the first column
                                            # Also your parts have the PLC and Part Number
                                            # ie. LND EX-0122-07.
CSV_TO_READ_DEL = '\t'                      # The delimer on the CSV you are reading
CSV_TO_WRITE = 'Sixbit_' + time.strftime('%m%d') + '_Fitments_To_Add.csv'        # Name of the CSV we will output the part numbers too
CSV_TO_WRITE_DEL = ','                      # The delimiter you would like to use on the output CSV
ODBC_CONN_NAME = 'SixBit'                   # The ODBC connection name
ODBC_USER = 'sa_sb'                         # The ODBC user name, '' if there is no user required
ODBC_PASS = 'S1xb1tR0x'                     # The ODBC pass, '' if there is no password required
# Change the stuff above this text

#SQL Statement to get Fitment Info
SQL_Fitment = "SELECT N.C.value('Name[1]', 'nvarchar(max)') AS Field, N.C.value('Value[1]', 'nvarchar(max)') AS Value FROM SixBit_KMJPERFORMANCE.dbo.CompatibilitySets CROSS APPLY CompatibilitySetDefinition.nodes('/Compatibilities/Compatibility/NameValue') N(C) INNER JOIN SixBit_KMJPERFORMANCE.dbo.ItemsEbay ON CompatibilitySets.CompatibilitySetID = ItemsEbay.CompatibilitySetID INNER JOIN SixBit_KMJPERFORMANCE.dbo.Inventory ON Inventory.ItemID = ItemsEbay.ItemID WHERE Inventory.SKU = ? AND ItemsEbay.CompatibilitySetID <> 1";
ODBC_CONN = open_odbc_connection(ODBC_CONN_NAME,ODBC_USER,ODBC_PASS)
print("Connection Established")

out_file = open(CSV_TO_WRITE, "w",newline='')
new_sku_csv = csv.writer(out_file,delimiter=CSV_TO_WRITE_DEL)
print("Creating New Fitments_To_Add.csv")

#write headers
#toWrite = []
#toWrite.append('year')
#toWrite.append('make')
#toWrite.append('model')
#toWrite.append('trim')
#toWrite.append('engine')
#toWrite.append('sku')
#new_sku_csv.writerow(toWrite)

with open(CSV_TO_READ) as f: # Open the csv
    cr = csv.reader(f, delimiter=CSV_TO_READ_DEL) # Set the delimiter on the CSV
    for row in cr:  # Scan through the rows in the CSV
        toWrite = []
        print(row[0])
        #get Fitments for SKU in CSV
        results = make_SQL_call(ODBC_CONN,SQL_Fitment,row[0])
        all_makes = results[0]
        all_models = results[1]
        all_years = results[2]
        all_trims = results[3]
        all_engines = results[4]

        compatibility_number = 0

        for record in all_makes:
            toWrite = []
            #add columns for each fitment
            toWrite.append(all_years[compatibility_number])
            toWrite.append(all_makes[compatibility_number])
            toWrite.append(all_models[compatibility_number])
            toWrite.append(all_trims[compatibility_number])
            toWrite.append(all_engines[compatibility_number])
            toWrite.append(row[0])

            print(toWrite)

            new_sku_csv.writerow(toWrite)

            compatibility_number+=1

    f.close()
    out_file.close()


print('This script executed successfully')
print('This script will close in 5 seconds')
time.sleep(1)
print('1..')
time.sleep(1)
print('2..')
time.sleep(1)
print('3..')
time.sleep(1)
print('4..')
time.sleep(1)
print('5..')

