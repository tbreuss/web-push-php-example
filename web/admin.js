document.addEventListener('DOMContentLoaded', () => {

  const sendPushButton = document.querySelector('#send-push-button');
  if (!sendPushButton) {
    return;
  }

  sendPushButton.addEventListener('click', () =>
        fetch('backend/notification_broadcast.php', {
          method: 'POST',
        })
          .then((response) => response.json())
          .then((data) => {
            const table = document.getElementById('results');
            for (const item of data) {
              const row = document.createElement("tr");
              
              const cell1 = document.createElement("td")
              cell1.appendChild(document.createTextNode(item[0]));
              row.appendChild(cell1);

              const cell2 = document.createElement("td")
              cell2.appendChild(document.createTextNode(item[1]));
              row.appendChild(cell2);

              const cell3 = document.createElement("td")
              cell3.appendChild(document.createTextNode(item[2]));
              row.appendChild(cell3);

              table.prepend(row);
            }
          })
  );
  
});
