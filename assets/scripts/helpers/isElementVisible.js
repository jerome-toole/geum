export default function isElementVisible(element) {
    const styles = window.getComputedStyle(element);

    return styles.getPropertyValue('display') !== 'none';
}
