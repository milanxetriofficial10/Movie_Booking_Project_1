// app.js - seat rendering and selection
document.addEventListener('DOMContentLoaded', function(){
  const map = document.getElementById('seat-map');
  if(!map) return;
  const rows = parseInt(map.dataset.rows);
  const cols = parseInt(map.dataset.cols);
  const price = parseFloat(map.dataset.price);
  const showId = map.dataset.show;
  const booked = window.booked || [];
  const selected = new Set();

  function seatId(r,c){ return String.fromCharCode(65 + r) + (c+1); }

  const table = document.createElement('div');
  table.className = 'seat-grid';
  for(let r=0;r<rows;r++){
    const rowEl = document.createElement('div');
    rowEl.className = 'seat-row';
    for(let c=0;c<cols;c++){
      const id = seatId(r,c);
      const seat = document.createElement('button');
      seat.className = 'seat';
      seat.type = 'button';
      seat.innerText = id;
      if(booked.includes(id)){
        seat.classList.add('booked');
        seat.disabled = true;
      } else {
        seat.addEventListener('click', ()=>{
          if(seat.classList.contains('selected')){
            seat.classList.remove('selected');
            selected.delete(id);
          } else {
            seat.classList.add('selected');
            selected.add(id);
          }
          updateSummary();
        });
      }
      rowEl.appendChild(seat);
    }
    table.appendChild(rowEl);
  }
  map.appendChild(table);

  function updateSummary(){
    const arr = Array.from(selected);
    document.getElementById('selected-seats').value = JSON.stringify(arr);
    document.getElementById('total').innerText = (arr.length * price).toFixed(2);
  }
});
