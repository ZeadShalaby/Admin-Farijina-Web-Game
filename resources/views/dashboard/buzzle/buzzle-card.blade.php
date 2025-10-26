<div id="puzzle-container" class="puzzle-container">
    <div id="secret-layer"></div>
</div>

<style>
body, html {
    height: 100%;
    margin: 0;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #7a8182;
    font-family: Arial, sans-serif;
}

.puzzle-container {
    width: 400px;
    height: 400px;
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    grid-template-rows: repeat(4, 1fr);
    gap: 2px;
    border: 2px solid #333;
    position: relative;
    box-shadow: 0 0 20px rgba(0,0,0,0.2);
    border-radius: 8px;
    overflow: hidden;
}

/* secret layer behind puzzle */
#secret-layer {
    position: absolute;
    top:0;
    left:0;
    width:100%;
    height:100%;
    background: linear-gradient(45deg, #ff9a9e, #fad0c4);
    z-index: 0;
    border-radius: 8px;
    opacity: 0.2;
}

.puzzle-piece {
    width: 100%;
    height: 100%;
    background-size: 400px 400px;
    cursor: pointer;
    transition: transform 0.2s, border 0.2s;
    z-index: 1;
}
.puzzle-piece:hover {
    transform: scale(1.05);
    z-index: 2;
}
</style>

<script>
const rows = 4;
const cols = 4;
const puzzleContainer = document.getElementById('puzzle-container');
const imgURL = 'https://picsum.photos/400';

// create pieces
let pieces = [];
for(let r=0; r<rows; r++){
    for(let c=0; c<cols; c++){
        const piece = document.createElement('div');
        piece.classList.add('puzzle-piece');
        piece.style.backgroundImage = `url(${imgURL})`;
        piece.style.backgroundPosition = `-${c*100}px -${r*100}px`;
        piece.dataset.row = r;
        piece.dataset.col = c;
        puzzleContainer.appendChild(piece);
        pieces.push(piece);
    }
}

// shuffle function
function shufflePieces() {
    pieces.sort(() => Math.random() - 0.5).forEach(p => puzzleContainer.appendChild(p));
}

// check if puzzle solved
function isPuzzleSolved() {
    return pieces.every(piece => piece.style.backgroundPosition === `-${piece.dataset.col*100}px -${piece.dataset.row*100}px`);
}

// swap pieces on click
let selected = null;
pieces.forEach(piece => {
    piece.addEventListener('click', () => {
        if(!selected){
            selected = piece;
            piece.style.border = '2px solid yellow';
        } else {
            const temp = selected.style.backgroundPosition;
            selected.style.backgroundPosition = piece.style.backgroundPosition;
            piece.style.backgroundPosition = temp;
            selected.style.border = 'none';
            selected = null;

            // check if solved
            if(isPuzzleSolved()) {
                // حل البازل -> اعادة ترتيب
                setTimeout(() => {
                    shufflePieces();
                }, 500);
            }
        }
    });
});

// initial shuffle
shufflePieces();
</script>
