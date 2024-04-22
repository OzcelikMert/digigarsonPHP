<html style="width: 80mm;height: auto; overflow: hidden; overflow-y: scroll;" ><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">

  <style>
    @page {  margin: 0;  padding: 0  }  @media print {.no-print, .no-print * {display: none !important }}
    ::-webkit-scrollbar {width: 0 !important;}
    * {margin: 0 auto;padding: 0}
    .invoice {margin-left: 0 auto;display: block;width: 72mm;padding: 0;font-family: Tahoma}

    .header {padding-bottom: 2mm}
    .body {padding: 2mm 0}
    .w-100 {width: 100% !important}
    .w-50 {width: 50% !important}
    .d-block {display: block !important}
    .d-inline-block {display: inline-block !important}
    .mt-1 {margin-top: 1mm}
    .mt-2 {margin-top: 2mm}
    .mt-3 {margin-top: 3mm}
    .mt-4 {margin-top: 4mm}
    .mt-5 {margin-top: 5mm}
    .mb-1 {margin-bottom: 1mm}
    .mb-2 {margin-bottom: 2mm}
    .mb-3 {margin-bottom: 3mm}
    .mb-4 {margin-bottom: 4mm}
    .mb-5 {margin-bottom: 5mm}
    .ml-1 {margin-left: 1mm}
    .ml-2 {margin-left: 2mm}
    .ml-3 {margin-left: 3mm}
    .ml-4 {margin-left: 4mm}
    .ml-5 {margin-left: 5mm}
    .border-xs {border-width: .2mm !important}
    .border-xm {border-width: .5mm !important}
    .border-xl {border-width: 1mm !important}
    .border-solid {border-style: solid !important}
    .border-bottom {border-bottom: .2mm solid #000}
    .border-top {border-top: .2mm solid #000}
    .bold {font-weight: 700 !important}
    .font-size-xxxs {font-size: 3mm !important}
    .font-size-xxs {font-size: 3.4mm !important}
    .font-size-x {font-size: 3.8mm !important}
    .font-size-xs {font-size: 4.2mm !important}
    .font-size-xm {font-size: 5mm !important}
    .font-size-xxm {font-size: 5.5mm !important}
    .font-size-xl {font-size: 7.5mm !important}
    .text-center {text-align: center !important}
    .text-left {text-align: left !important}
    .text-right {text-align: right !important}
    .float-left {float: left !important}
    .float-right {float: right !important}
  </style>

</head>
<body>
  <div id="print" style="width: 80mm;height: auto; background-color: #ffffff;">
      <?=$_POST["elements"]?>
  </div>
</body>
</html>