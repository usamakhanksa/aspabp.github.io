In this section, step-by-step instructions will be demonstrated towards acquiring a Signed Invoice.

SHA-256 Hash - Hashing algorithm
The main reason for using SHA-256 is to strengthen the security and protect the data by ensuring it doesn’t have any known vulnerabilities that make it insecure, and it has not been “broken” unlike some other popular hashing algorithms.
The output of a hashing algorithm SHA256 will always be the same, consisting of 256 bits (32 bytes), which is displayed as 64 alphanumeric characters.
Signing steps
Step 1: Generate Invoice Hash
To generate the invoice hash below steps can be followed:

Open the invoice XML file.
Remove the tags mentioned in the table below using the XPath.
Remove the XML version.
Canonicalize the Invoice using the C14N11 standard.
Hash the new invoice body using SHA-256 (output). e.g.:a11b6fe587a50f7daffe3a7fb42dcccf- 32b43ee9b37d9f252d04243e54c11a3f
Encode the hashed invoice using base64 (output)
Using HEX-to Base64 Encoder e.g.:oRtv5YelD32v/jp/tC3MzzK0PumzfZ8lLQQkPlTBGj8= Note: All these values will be used in later steps.
Note:
-All these values will be used in later steps.
-Please make sure that you have a copy of the original invoice before removing the above tags
Tags to be removed from invoice	XPath, Use this path to find the target tag
UBLExtension	*[local-name()=׳Invoice׳]//*[local-name()=׳UBLExtensions׳]
QR	//*[local-name()=׳AdditionalDocumentReference׳] [cbc:ID[normalize-space(text()) = ׳QR׳]]
Signature	*[local-name()=׳Invoice׳]//*[local-name()=׳Signature׳]
Step 2: Generate Digital Signature

Generate private key from CSR config file (you can refer to openssl commands, or readme file on SDK)
Sign the generated invoice hash (in SHA-256 format not encoded with base64) with ECDSA using the private key (output). e.g.:MEQCIGvLa1f3uMCe0AidKUWJ5ghMiDMRcC0qO78ntcTKVOYgAiAKBkX+uuFhbIcye3JznNa45qH1twlLFu/qPzEQ9HMNLw==
Note: This value will be used in later steps.

Values to be used
Generated Invoice Hash from 1st step (in SHA-256 format not encoded with base64)
Private key
Step 3: Generate Certificate Hash

Hash the certificate using SHA-256 (output). e.g.:69a95fc237b42714dc4457a33b94cc452fd9f- 110504c683c401144d9544894fb
Encode the hashed x509 certificate using base64 (ENCODER BASE64 ) (output).
e.g.:NjlhOTVmYzIzN2I0MjcxNGRjNDQ1N2EzM2I5NGNjNDUyZmQ5ZjExMDUwNGM2ODNjNDAx- MTQ0ZDk1NDQ4OTRmYg==
Note: final output will be used in later steps
.

Values to be used
X509 Certificate( After completing CCSID API, it will return (binary security token), take this value and decode it using base 64, the output is X509 certificate.)
Step 4: Populate the Signed Properties Output

Open the invoice before 1st step (before getting tags removed).
Refer to the below table to fill mentioned fields with their corresponding values using the related Xpath, (if there are any old values already exist in the fields, please make sure to remove all of them and replace them with the new values only).
To get X509 Serial number, decode the X509 certificate the value will be printed in the decoded result.
Notes:
● Populated Signed Properties will be used in the next step.

Signed Properties tag.
You should use this tag in the next step.

<xades:SignedProperties xmlns:xades=“Assigned ETSI XML URIs” Id=“xadesSignedProperties”>
xades:SignedSignatureProperties
xades:SigningTime</xades:SigningTime>
xades:SigningCertificate
xades:Cert
xades:CertDigest
<ds:DigestMethod xmlns:ds=“XML-Signature Syntax and Processing” Algorithm=“XML Encryption Syntax and Processing”/>
<ds:DigestValue xmlns:ds=“XML-Signature Syntax and Processing”></ds:DigestValue>
</xades:CertDigest>
xades:IssuerSerial
<ds:X509IssuerName xmlns:ds=“XML-Signature Syntax and Processing”></ds:X509IssuerName>
<ds:X509SerialNumber xmlns:ds=“XML-Signature Syntax and Processing”></ds:X509SerialNumber>
</xades:IssuerSerial>
</xades:Cert>
</xades:SigningCertificate>
</xades:SignedSignatureProperties>
</xades:SignedProperties>

Note:
-You shouldn’t include the above tag in the invoice, we just using it to populate Signed Properties Hash.

Fields	Values	XPath
DigestValue	Encoded certificate hashed from Step 3	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Exten- sionContent/sig:UBLDocumentSignatures/sac:SignatureInfor- mation/ds:Signature/ds:Object/xades:QualifyingProperties/ xades:SignedProperties/xades:SignedSignatureProperties/ xades:SigningCertificate/xades:Cert/xades:CertDigest/ds:Di- gestValue
SigningTime	Sign timestamp as current datetime	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Exten- sionContent/sig:UBLDocumentSignatures/sac:SignatureInfor- mation/ds:Signature/ds:Object/xades:QualifyingProperties/ xades:SignedProperties/xades:SignedSignatureProperties/ xades:SigningTime
X509IssuerName	Certificate issuer name From the certificate (decoded)	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Exten- sionContent/sig:UBLDocumentSignatures/sac:SignatureInfor- mation/ds:Signature/ds:Object/xades:QualifyingProperties/ xades:SignedProperties/xades:SignedSignatureProperties/ xades:SigningCertificate/xades:Cert/xades:IssuerSerial/ds:X- 509IssuerName
X509SerialNum- ber	Certificate serial number From the certificate (decoded)	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Exten- sionContent/sig:UBLDocumentSignatures/sac:SignatureInfor- mation/ds:Signature/ds:Object/xades:QualifyingProperties/ xades:SignedProperties//xades:SignedSignatureProperties/ xades:SigningCertificate/xades:Cert/xades:IssuerSerial/ds:X- 509SerialNumber
Step 5: Generate Signed Properties Hash

To generate the Signed Properties Hash, you should use the tag provided on the previous page and fill in the Populated Signed Properties in step 4 (using the same values).
Hash the new property tag(After fill) using SHA-256 (output).
e.g.:99282555b5d79209be5883cc23eb234cd01bd33ea7d54d88f491248d33e321f1
Encode the hashed property using base64 (ENCODER BASE64 ) (output).
e.g.:OTkyODI1NTViNWQ3OTIwOWJlNTg4M2NjMjNlYjIzNGNkMDFiZDMzZWE3ZDU0ZDg4ZjQ5MTI0OGQzM2UzMjFmMQ==
Step 6: Populate The UBL Extensions Output

Use the invoice XML file acquired after completing the 4th step.
Refer to the below table to fill mentioned UBL-Extensions tag’s fields with their corresponding values using the related XPath.
Note: if there are any old values already exist in the fields, please make sure to remove all of them and replace them with the new values only.

Fields	Values	XPath
SignatureValue	Digital Signature from Step 2	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Extension- Content/sig:UBLDocumentSignatures/sac:SignatureInforma- tion/ds:Signature/ds:SignatureValue
X509Certificate	Certificate	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Extension- Content/sig:UBLDocumentSignatures/sac:SignatureInforma- tion/ds:Signature/ds:KeyInfo/ds:X509Data/ds:X509Certificate
DigestValue	Encoded signed Properties hash from Step 5	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Extension- Content/sig:UBLDocumentSignatures/sac:SignatureInforma- tion/ds:Signature/ds:SignedInfo/ds:Reference[@URI=׳#xades- SignedProperties׳]/ds:DigestValue
DigestValue	Encoded invoice hash from Step 1	/Invoice/ext:UBLExtensions/ext:UBLExtension/ext:Extension- Content/sig:UBLDocumentSignatures/sac:SignatureInforma- tion/ds:Signature/ds:SignedInfo/ds:Reference[@Id=׳invoice- SignedData׳]/ds:DigestValue
Generate QR & Populate Encoded QR:
Final step in the signing process. Please refer to the document shared on QR.
Document Name: QR Code Format & Structure

Appendix
Openssl commands & urls that can be useful

Openssl:
Hash function: openssl dgst -sha256 <xml_file_name>
Generate private key: openssl ecparam -name secp256k1 -genkey -noout -out PrivateKey.pem
Generate public key: openssl ec -in PrivateKey.pem -pubout -conv_form compressed -out PublicKey.pem
Generate csr: openssl req -new -sha256 -key privateKey.pem -extensions v3_req -config config.cnf -out taxpayer.csr