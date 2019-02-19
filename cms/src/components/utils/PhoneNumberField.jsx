import React from "react";
import MaskedInput from "react-text-mask";

function PhoneNumberField(props) {
    const { inputRef, ...other } = props;

    return (
        <MaskedInput
            {...other}
            ref={ref => inputRef(ref ? ref.inputElement : null)}
            mask={["8", " ", "(", /\d/, /\d/, /\d/, ")", " ", /\d/, /\d/, /\d/, "-", /\d/, /\d/, "-", /\d/, /\d/]} //s
            placeholderChar={"\u2000"}
            showMask
        />
    );
}

export default PhoneNumberField;