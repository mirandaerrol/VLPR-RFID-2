<script>
    const LIVE_URL = "{{ route('live.detection') }}";

    function fetchPlates() {
        fetch(LIVE_URL)
            .then(res => res.json())
            .then(data => {
                let output = document.getElementById("plates-output");
                if (data.plates && data.plates.length > 0) {
                    output.innerHTML = data.plates.map(p => `<li>${p}</li>`).join("");
                } else {
                    output.innerHTML = "<li>No plates detected</li>";
                }
            })
            .catch(err => console.error("Live fetch error:", err));
    }

    
    setInterval(fetchPlates, 2000);
</script>

<ul id="plates-output"></ul>