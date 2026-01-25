export default function ie11() {
    const UAString = navigator.userAgent;
    return UAString.indexOf('Trident') !== -1 && UAString.indexOf('rv:11') !== -1;
}
