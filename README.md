# IntelliExport: AI Data Preparation Tool

IntelliExport is a powerful AI Data Preparation Tool designed to export data from specified database views and generate various output formats including PDF, CSV, and XLSX.

## Features

- Export data from multiple database views
- Generate PDF reports with customizable layouts
- Export data to CSV format
- Export data to XLSX format (Excel)
- Debug mode for troubleshooting
- Configurable through INI files

## Requirements

- PHP 7.4 or higher
- Composer
- MySQL/MariaDB database

## Installation

1. Clone the repository:
   ```
   git clone https://github.com/yourusername/intelliexport.git
   cd intelliexport
   ```

2. Install dependencies using Composer:
   ```
   composer install
   ```

3. Make the main script executable:
   ```
   chmod +x intelliexport.sh
   ```

4. Copy the example configuration file and adjust it to your needs:
   ```
   cp config.example.ini config.ini
   ```

## Usage

The basic syntax for using IntelliExport is:

```
./intelliexport.sh [options] <database:view1> [database:view2] ...
```

### Options:

- `-c, --config <file>`: Specify a custom config file (default: config.ini)
- `-d, --debug`: Enable debug mode
- `-h, --help`: Show the help message

### Arguments:

- `database:view`: Specify the database and view to export. If no database is specified, 'default' will be used.

### Examples:

1. Export data from a single view:
   ```
   ./intelliexport.sh default:MARKETING_VIEW
   ```

2. Export data from multiple views with a custom config file and debug mode:
   ```
   ./intelliexport.sh -c custom_config.ini -d sales:ORDER_VIEW marketing:PRODUCT_VIEW
   ```

## Configuration

The `config.ini` file contains important settings for database connections, export options, and other parameters. Make sure to review and adjust this file according to your environment and requirements.

## Output

Exported files will be saved in the `export` directory with timestamps in their filenames:

- PDF files: `{database}_{view}_YYYY-MM-DD_HHMM.pdf`
- CSV files: `{database}_{view}_YYYY-MM-DD_HHMM.csv`
- XLSX files: `{database}_{view}_YYYY-MM-DD_HHMM.xlsx`

## Troubleshooting

If you encounter any issues, try running the script with the debug option (`-d`) for more detailed output. If the problem persists, please open an issue on the GitHub repository with a detailed description of the error and your configuration (make sure to remove any sensitive information).

## Contributing

Contributions are welcome! Please feel free to submit a Pull Request.

## License

This project is licensed under the MIT License.
