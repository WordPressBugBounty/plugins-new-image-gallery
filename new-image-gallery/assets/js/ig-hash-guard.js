/**
 * Image Gallery - Hash Guard
 * Prevents SyntaxErrors in third-party scripts that attempt to use 
 * PhotoSwipe's malformed URL hashes (#&gid=...) as CSS selectors.
 */
(function() {
    function clearMalformedHash() {
        var hash = window.location.hash;
        // If hash starts with #&, it's a PhotoSwipe hash that crashes querySelector
        if (hash && hash.indexOf('&gid=') !== -1) {
            if (window.history.replaceState) {
                // Remove the hash without reloading the page
                window.history.replaceState(null, null, window.location.pathname + window.location.search);
            } else {
                window.location.hash = '';
            }
            console.log('IGP: Malformed hash cleared to prevent theme script conflicts.');
        }
    }

    // Run immediately
    clearMalformedHash();

    // Also listen for hash changes in case another script adds it back
    window.addEventListener('hashchange', clearMalformedHash);
})();
