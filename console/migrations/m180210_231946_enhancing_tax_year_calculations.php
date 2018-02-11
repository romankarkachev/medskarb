<?php

use yii\db\Migration;

/**
 * Добавляются колонки для выгрузки в годовую декларацию.
 */
class m180210_231946_enhancing_tax_year_calculations extends Migration
{
    public function safeUp()
    {
        $this->addColumn('tax_year_calculations', 'paid_fact', $this->decimal(12, 2)->comment('Доплачено фактически'). ' AFTER `declared_at`');
        $this->addColumn('tax_year_calculations', 'tdm', $this->text()->comment('Данные для декларации в json') . ' AFTER `calculation_details`');

        // показатели для декларации
        // Сумма исчисленного налога (авансового платежа по налогу)
        $this->addColumn('tax_year_calculations', 'tdr273', $this->decimal(12, 2)->comment('Налог исчисленный за налоговый период') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr272', $this->decimal(12, 2)->comment('Налог исчисленный за девять месяцев') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr271', $this->decimal(12, 2)->comment('Налог исчисленный за полугодие') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr270', $this->decimal(12, 2)->comment('Налог исчисленный за первый квартал') . ' AFTER `amount_to_pay`');

        // Налоговая база для исчисления налога (авансового платежа по налогу)
        $this->addColumn('tax_year_calculations', 'tdr243', $this->decimal(12, 2)->comment('Налоговая база за налоговый период') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr242', $this->decimal(12, 2)->comment('Налоговая база за девять месяцев') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr241', $this->decimal(12, 2)->comment('Налоговая база за полугодие') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr240', $this->decimal(12, 2)->comment('Налоговая база за первый квартал') . ' AFTER `amount_to_pay`');

        // Сумма произведенных расходов нарастающим итогом
        $this->addColumn('tax_year_calculations', 'tdr223', $this->decimal(12, 2)->comment('Расходы за налоговый период') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr222', $this->decimal(12, 2)->comment('Расходы за девять месяцев') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr221', $this->decimal(12, 2)->comment('Расходы за полугодие') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr220', $this->decimal(12, 2)->comment('Расходы за первый квартал') . ' AFTER `amount_to_pay`');

        // Сумма полученных доходов нарастающим итогом
        $this->addColumn('tax_year_calculations', 'tdr213', $this->decimal(12, 2)->comment('Доходы за налоговый период') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr212', $this->decimal(12, 2)->comment('Доходы за девять месяцев') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr211', $this->decimal(12, 2)->comment('Доходы за полугодие') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr210', $this->decimal(12, 2)->comment('Доходы за первый квартал') . ' AFTER `amount_to_pay`');

        $this->addColumn('tax_year_calculations', 'tdr100', $this->decimal(12, 2)->comment('Сумма налога, подлежащая доплате за налоговый период') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr070', $this->decimal(12, 2)->comment('Сумма авансового платежа III квартал') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr040', $this->decimal(12, 2)->comment('Сумма авансового платежа II квартал') . ' AFTER `amount_to_pay`');
        $this->addColumn('tax_year_calculations', 'tdr020', $this->decimal(12, 2)->comment('Сумма авансового платежа I квартал') . ' AFTER `amount_to_pay`');
    }

    public function safeDown()
    {
        $this->dropColumn('tax_year_calculations', 'paid_fact');
        $this->dropColumn('tax_year_calculations', 'tdm');

        $this->dropColumn('tax_year_calculations', 'tdr273');
        $this->dropColumn('tax_year_calculations', 'tdr272');
        $this->dropColumn('tax_year_calculations', 'tdr271');
        $this->dropColumn('tax_year_calculations', 'tdr270');

        $this->dropColumn('tax_year_calculations', 'tdr243');
        $this->dropColumn('tax_year_calculations', 'tdr242');
        $this->dropColumn('tax_year_calculations', 'tdr241');
        $this->dropColumn('tax_year_calculations', 'tdr240');

        $this->dropColumn('tax_year_calculations', 'tdr223');
        $this->dropColumn('tax_year_calculations', 'tdr222');
        $this->dropColumn('tax_year_calculations', 'tdr221');
        $this->dropColumn('tax_year_calculations', 'tdr220');

        $this->dropColumn('tax_year_calculations', 'tdr213');
        $this->dropColumn('tax_year_calculations', 'tdr212');
        $this->dropColumn('tax_year_calculations', 'tdr211');
        $this->dropColumn('tax_year_calculations', 'tdr210');

        $this->dropColumn('tax_year_calculations', 'tdr100');
        $this->dropColumn('tax_year_calculations', 'tdr070');
        $this->dropColumn('tax_year_calculations', 'tdr040');
        $this->dropColumn('tax_year_calculations', 'tdr020');
    }
}
