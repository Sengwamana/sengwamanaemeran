$(document).ready(() => {
    class CustomField {
        $customFieldOptions = $('#custom-field-options')
        $customFieldsBox = $('#custom_fields_box')

        init() {
            this.sortable()
            this.handleType()
            this.addNewRow()
            this.removeRow()
        }

        handleType() {
            const $customFieldsBox = this.$customFieldsBox
            const $type = $('.custom-field-type')
            const $placeholder = $('#placeholder').closest('.form-group')

            if ($type.val() === 'text' || $type.val() === 'textarea' ||  $type.val() === 'number') {
                this.$customFieldsBox.hide()
                $placeholder.show()
            } else {
                this.$customFieldsBox.show()
                $placeholder.hide()
            }

            $type.change(function () {
                if ($(this).val() === 'text' || $(this).val() === 'textarea' || $type.val() === 'number') {
                    $customFieldsBox.hide()
                    $placeholder.show()
                    return
                }

                $customFieldsBox.show()
                $placeholder.hide()
            })
        }

        sortable() {
            $('.option-sortable').sortable({
                stop: () => {
                    $('.option-sortable').sortable('toArray', {attribute: 'data-index'}).map((id, index) => {
                        $(`.option-row[data-index="${id}"]`).find('.option-order').val(index)
                    })
                }
            });
        }

        addNewRow() {
            this.$customFieldOptions.on('click', '#add-new-row', function () {
                const table = $(this).parent().find('table tbody');
                const tr = table.find('tr').last().clone();

                const label = `options[${table.find('tr').length}][label]`
                const value = `options[${table.find('tr').length}][value]`

                tr.find('.option-label').val('').attr('name', label)
                tr.find('.option-value').val('').attr('name', value)
                table.append(tr);
            });
        }

        removeRow() {
            this.$customFieldOptions.on('click', '.remove-row', function () {
                const self = $(this).parent().parent()
                const parent = self.parent();

                const tr = parent.find('tr')

                if (tr.length <= 1) {
                    tr.find('.option-label').val('')
                    tr.find('.option-value').val('')

                    return
                }

                self.remove()
            })
        }
    }

    new CustomField().init()
})
