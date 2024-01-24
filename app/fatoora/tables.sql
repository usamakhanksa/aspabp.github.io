CREATE TABLE Emtyaz.[Fatoora].[POSInvoice]
(
    [RecID] int IDENTITY(1,1) PRIMARY KEY,
    [InvoiceNumber] varchar(255),
    [InvoiceHash] varchar(255),
    [UUID] varchar(255),
    [QR] varchar(255),
    [Stamp] varchar(255),
    [PIH] varchar(255),
    [Invoice] nvarchar(max),
    [CreationStatusRecID] int,
    [ReportingStatusRecID] int
)

CREATE TABLE Emtyaz.[Fatoora].[BusinessInvoice]
(
    [RecID] int IDENTITY(1,1) PRIMARY KEY,
    [InvoiceNumber] varchar(255),
    [InvoiceHash] varchar(255),
    [UUID] varchar(255),
    [QR] varchar(255),
    [Stamp] varchar(255),
    [PIH] varchar(255),
    [Invoice] nvarchar(max),
    [CreationStatusRecID] int,
    [ReportingStatusRecID] int
)

CREATE TABLE Emtyaz.Fatoora.CSIDTemp
(
    RecID INT IDENTITY(1,1) PRIMARY KEY,
    CSR NVARCHAR(MAX),
    Secret NVARCHAR(MAX),
    BinarySecurityToken NVARCHAR(MAX),
    CreatedDate DATETIME,
    ExpireDate DATETIME,
    RequestID BIGINT,
    CONSTRAINT UC_RequestID UNIQUE (RequestID)
);

CREATE TABLE Emtyaz.Fatoora.CSIDProduction
(
    RecID INT IDENTITY(1,1) PRIMARY KEY,
    CSR NVARCHAR(MAX),
    Secret NVARCHAR(MAX),
    BinarySecurityToken NVARCHAR(MAX),
    CreatedDate DATETIME,
    ExpireDate DATETIME,
    RequestID BIGINT,
    RequestedSecurityToken NVARCHAR(MAX),
    Status NVARCHAR(50),
    CONSTRAINT UCP_RequestID UNIQUE (RequestID)
);

CREATE TABLE Emtyaz.Fatoora.CSIDStatus
(
    RecID INT IDENTITY(1,1) PRIMARY KEY,
    Code NVARCHAR(20) UNIQUE,
    Name NVARCHAR(50),
    Description NVARCHAR(MAX),
    CreatedDate DATETIME
);

INSERT INTO Emtyaz.Fatoora.CSIDStatus
    (Code, Name, Description, CreatedDate)
VALUES
    ('ACTIVE', 'Active', 'The item is currently active', GETDATE()),
    ('INACTIVE', 'Inactive', 'The item is currently inactive', GETDATE()),
    ('PENDING', 'Pending', 'The item is pending', GETDATE());


CREATE TABLE Emtyaz.Fatoora.FatooraSettings (
    id INT IDENTITY(1,1) PRIMARY KEY,
    cnf NVARCHAR(MAX),
    private_key NVARCHAR(MAX),
    public_key NVARCHAR(MAX),
    csr NVARCHAR(MAX),
    cert_production NVARCHAR(MAX),
    secret_production NVARCHAR(MAX),
    csid_id_production NVARCHAR(MAX),
    cert_compliance NVARCHAR(MAX),
    secret_compliance NVARCHAR(MAX),
    csid_id_compliance NVARCHAR(MAX)
);

CREATE TABLE Emtyaz.[Fatoora].[ReportingStatus]
(
    [RecID] int IDENTITY(1,1) PRIMARY KEY,
    [StatusCode] varchar(50) NOT NULL,
    [Description] varchar(255) NOT NULL
);

CREATE TABLE Emtyaz.[Fatoora].[CreationStatus]
(
    [RecID] int IDENTITY(1,1) PRIMARY KEY,
    [StatusCode] varchar(50) NOT NULL,
    [Description] varchar(255) NOT NULL
);


INSERT INTO Emtyaz.[Fatoora].[ReportingStatus] (StatusCode, Description)
VALUES ('Success', 'Creation process successful'),
       ('Failure', 'Creation process failed');


INSERT INTO Emtyaz.[Fatoora].[CreationStatus] (StatusCode, Description)
VALUES ('Pending', 'Pending reporting process'),
       ('Created', 'Invoice created'),
       ('Reported', 'Invoice reported successfully');
