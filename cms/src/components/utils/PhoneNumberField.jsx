import React from "react";
import MaskedInput from "react-text-mask";

function PhoneNumberField(props) {
    const { inputRef, value, ...other } = props;
    let parsedValue = value || '';

    if (parsedValue[0] === '7') {
        parsedValue = parsedValue.substr(1 - parsedValue.length)
    }

    return (
        <MaskedInput
            value={parsedValue}
            {...other}
            ref={ref => inputRef(ref ? ref.inputElement : null)}
            mask={["8", " ", "(", /\d/, /\d/, /\d/, ")", " ", /\d/, /\d/, /\d/, "-", /\d/, /\d/, "-", /\d/, /\d/]} //s
            placeholderChar={"\u2000"}
            showMask
        />
    );
}

export default PhoneNumberField;