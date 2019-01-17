import React from "react";
import PropTypes from "prop-types";
import toNumber from "lodash/toNumber";

class Money extends React.Component {

    parseValue() {
        const { value, currency } = this.props;
        const { delimiter, prefix, suffix } = types[currency];
        let res;

        // Убираем копейки
        res = toNumber(value).toFixed(0);

        // Добавляем разделитель
        res = res.replace(/(\d)(?=(\d\d\d)+([^\d]|$))/g, "$1" + delimiter);

        // Добавляем денежный знак
        res = `${prefix}${res}${suffix}`;

        return res;
    }

    render() {
        const parsedValue = this.parseValue();

        return (
            <span style={{whiteSpace: "nowrap"}}>
                {parsedValue}
            </span>
        );
    }
}

/**
 * Типовые настройки отображения валют
 */
const types = {
    "RUB": {
        delimiter: " ",
        prefix: "",
        suffix: "₽",
    },
};

Money.propTypes = {
    /**
     * Количество денег
     */
    value: PropTypes.oneOfType([PropTypes.number.isRequired, PropTypes.string.isRequired]),

    /**
     * Денежная единица
     * Проверяется наличие настроек для единицы в `types`
     */
    currency: (props, propName, componentName) => {
        if (types[props[propName]] === undefined) {
            return new Error(`Money type '${props[propName]}' not supported in component ${componentName}`);
        }
    },
};

Money.defaultProps = {
    /**
     * По-умолчанию отображаем рубли
     */
    currency: "RUB",
};

export default Money;