document.querySelectorAll('.add-option-btn').forEach(button => {
    button.addEventListener('click', function() {
        const questionId = this.getAttribute('data-question-id');
        // تعيين id السؤال في النموذج
        document.getElementById('question_id').value = questionId;

        // عرض النافذة المنبثقة
        const addOptionModal = new bootstrap.Modal(document.getElementById('addOptionModal'));
        addOptionModal.show();
    });
});
