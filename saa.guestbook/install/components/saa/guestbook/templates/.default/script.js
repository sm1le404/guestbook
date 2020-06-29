BX.ready(function () {
  var form = document.querySelector('#messageForm');
  form.addEventListener('submit', function(e){
    var formData = new FormData(form);
    e.preventDefault();
    BX.ajax.runComponentAction('saa:guestbook',
      'fastAjax', {
        mode: 'class',
        data: formData,
      })
      .then(function(response) {
        console.log(response);
        if (response.status === 'success') {
          alert('Ваше сообщение успешно добавлено');
          form.remove();
        } else {
         alert(response.errors[0].message);
        }
      });
  });
});