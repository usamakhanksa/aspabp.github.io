<html>
  <head>
    <meta http-equiv="Content-Type" content="text/html;charset=UTF-8" />
    <style>
      body {
        font-family: sans-serif;
        font-size: 12px;
      }
      p {
        margin: 0pt;
      }
      table {
        border-collapse: collapse;
        width: 100%;
      }
      .td {
        vertical-align: top;
      }
      .td-border {
      }
      .border-table {
        border: .5px solid #000000;
        border-radius: 2rem;
        padding: .5rem;
      }
      .header-info tbody tr td {
        padding-top: .5rem;
        padding-bottom: .5rem;
      }
      .value {
        margin-top: .3rem;
        padding: .3rem;
        background-color: rgb(247, 237, 226);
        border: .5px solid #fff1e2;
      }
    </style>
  </head>
  <body>
    <div class="border-table">
      <table width="100%">
        <tbody>
          <tr>
            <td
              style="font-size: 2rem; padding: 1rem"
              align="center"
              width="100%"
            >
              Tax Invoice فاتورة ضريبية
            </td>
            <td>
              <?php $data = 'taxtax'; echo $data; ?>
            </td>
          </tr>
        </tbody>
      </table>
      <div class="border-table" style="border: none; background-color: rgb(250, 241, 230);">
        <table width="100%">
          <tbody>
            <tr>
              <td
                style="font-size: 1.5rem; padding: 1rem"
                align="left"
                width="33%"
              >
                <table class="header-info">
                  <tbody>
                    <tr>
                      <td>
                        <div>Invoice Reference Number (IRN)</div>
                        <div>123456</div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div>Issue Date</div>
                        <div>2022-09-07</div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div>Supply Date</div>
                        <div>2022-09-08</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td
                style="font-size: 1.5rem; padding: 1rem"
                align="center"
                width="33%"
              >
                <img src="qrcode.png" alt="" width="150px" height="150px" />
              </td>
              <td
                style="font-size: 1.5rem; padding: 1rem"
                align="right"
                width="33%"
              >
                <table class="header-info" style="direction: rtl">
                  <tbody>
                    <tr>
                      <td>
                        <div>الرقم التسلسلي</div>
                        <div>123456</div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div>تاريخ اصدار الفاتورة</div>
                        <div>2022-09-07</div>
                      </td>
                    </tr>
                    <tr>
                      <td>
                        <div>تاريخ التوريد</div>
                        <div>2022-09-08</div>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <div class="border-table" style="margin-top: 1rem">
        <table width="100%">
          <tbody>
            <tr>
              <td
                align="left"
                colspan="2"
                style="padding: 1rem; font-size: 1.5rem; width: 50%"
              >
                Seller Identification
              </td>
              <td
                align="right"
                colspan="2"
                style="padding: 1rem; font-size: 1.5rem; width: 50%"
              >
                هوية المورد
              </td>
            </tr>
            <tr>
              <td
                align="left"
                colspan="2"
                style="padding: .5rem; padding-right: 0"
              >
                <div>Seler Name</div>
                <table>
                  <tbody>
                    <tr>
                      <td class="value">
                        Maximum Speed Tech Supply LTD
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
              <td
                align="right"
                colspan="2"
                style="padding: .5rem; padding-left: 0"
              >
                <div>اسم المورد</div>
                <table>
                  <tbody>
                    <tr>
                      <td class="value">
                        Maximum Speed Tech Supply LTD
                      </td>
                    </tr>
                  </tbody>
                </table>
              </td>
            </tr>
            <tr>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>VAT Registration</div>
                <table><tbody><tr><td class="value">399999999900003</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>تسجيل ضريبة القيمة المضافة</div>
                <table><tbody><tr><td class="value">399999999900003</td></tr></tbody></table>
              </td>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>Addition SellerID Type</div>
                <table><tbody><tr><td class="value">(CR)</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>نوع المعرفات الإضافية للمورد</div>
                <table><tbody><tr><td class="value">السجل التجاري</td></tr></tbody></table>
              </td>
            </tr>
            <tr>
              <td
                align="left"
                colspan="2"
                style="padding: .5rem; font-size: 1.5rem"
              >
                Address
              </td>
              <td
                align="right"
                colspan="2"
                style="padding: .5rem; font-size: 1.5rem"
              >
                لعنوان
              </td>
            </tr>
            <tr>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>Street Name</div>
                <table><tbody><tr><td class="value">Prince Sultan</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>اسم الشارع</div>
                <table><tbody><tr><td class="value">الامير سلطان</td></tr></tbody></table>
              </td>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>Building Number</div>
                <table><tbody><tr><td class="value">2322</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>رقم المبنى</div>
                <table><tbody><tr><td class="value">2322</td></tr></tbody></table>
              </td>
            </tr>
            <tr>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>City</div>
                <table><tbody><tr><td class="value">Riyadh</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>اسم المدينة</div>
                <table><tbody><tr><td class="value">الرياض</td></tr></tbody></table>
              </td>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>District</div>
                <table><tbody><tr><td class="value">Al-Murabba</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>اسم الحي</div>
                <table><tbody><tr><td class="value">المربع</td></tr></tbody></table>
              </td>
            </tr>
            <tr>
              <td align="left" style="padding: .5rem; padding-right: 0">
                <div>Country Code</div>
                <table><tbody><tr><td class="value">SA</td></tr></tbody></table>
              </td>
              <td align="right" style="padding: .5rem; padding-left: 0">
                <div>رمز الدولة</div>
                <table><tbody><tr><td class="value">SA</td></tr></tbody></table>
              </td>
            </tr>
          </tbody>
        </table>
        <div class="border-table" style="margin-top: 1rem">
          <table width="100%">
            <tbody>
              <tr>
                <td
                  align="left"
                  colspan="2"
                  style="padding: 1rem; font-size: 1.5rem; width: 50%"
                >
                  Buyer Identification
                </td>
                <td
                  align="right"
                  colspan="2"
                  style="padding: 1rem; font-size: 1.5rem; width: 50%"
                >
                  هوية المورد
                </td>
              </tr>
              <tr>
                <td
                  align="left"
                  colspan="2"
                  style="padding: .5rem; padding-right: 0"
                >
                  <div>Seler Name</div>
                  <table>
                    <tbody>
                      <tr>
                        <td class="value">
                          Maximum Speed Tech Supply LTD
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
                <td
                  align="right"
                  colspan="2"
                  style="padding: .5rem; padding-left: 0"
                >
                  <div>اسم المورد</div>
                  <table>
                    <tbody>
                      <tr>
                        <td class="value">
                          Maximum Speed Tech Supply LTD
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </td>
              </tr>
              <tr>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>VAT Registration</div>
                  <table><tbody><tr><td class="value">399999999900003</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>تسجيل ضريبة القيمة المضافة</div>
                  <table><tbody><tr><td class="value">399999999900003</td></tr></tbody></table>
                </td>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>Addition SellerID Type</div>
                  <table><tbody><tr><td class="value">(CR)</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>نوع المعرفات الإضافية للمورد</div>
                  <table><tbody><tr><td class="value">السجل التجاري</td></tr></tbody></table>
                </td>
              </tr>
              <tr>
                <td
                  align="left"
                  colspan="2"
                  style="padding: .5rem; font-size: 1.5rem"
                >
                  Address
                </td>
                <td
                  align="right"
                  colspan="2"
                  style="padding: .5rem; font-size: 1.5rem"
                >
                  لعنوان
                </td>
              </tr>
              <tr>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>Street Name</div>
                  <table><tbody><tr><td class="value">Prince Sultan</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>اسم الشارع</div>
                  <table><tbody><tr><td class="value">الامير سلطان</td></tr></tbody></table>
                </td>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>Building Number</div>
                  <table><tbody><tr><td class="value">2322</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>رقم المبنى</div>
                  <table><tbody><tr><td class="value">2322</td></tr></tbody></table>
                </td>
              </tr>
              <tr>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>City</div>
                  <table><tbody><tr><td class="value">Riyadh</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>اسم المدينة</div>
                  <table><tbody><tr><td class="value">الرياض</td></tr></tbody></table>
                </td>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>District</div>
                  <table><tbody><tr><td class="value">Al-Murabba</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>اسم الحي</div>
                  <table><tbody><tr><td class="value">المربع</td></tr></tbody></table>
                </td>
              </tr>
              <tr>
                <td align="left" style="padding: .5rem; padding-right: 0">
                  <div>Country Code</div>
                  <table><tbody><tr><td class="value">SA</td></tr></tbody></table>
                </td>
                <td align="right" style="padding: .5rem; padding-left: 0">
                  <div>رمز الدولة</div>
                  <table><tbody><tr><td class="value">SA</td></tr></tbody></table>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </body>
</html>
