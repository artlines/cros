import React from "react";
import PropTypes from "prop-types";
import {
    FormControl,
    FormHelperText,
    Input,
    InputLabel,
} from "@material-ui/core";
import CKEditor from "ckeditor4-react";

class WysiwygField extends React.PureComponent {
    render() {
        const { name, label, helperText, onChange, value, error, required } = this.props;

        return (
            <React.Fragment>
                <FormControl error={error} required={required}>
                    <InputLabel htmlFor={name}>{label}</InputLabel>
                    <div style={{ marginTop: 48 }}>
                        <CKEditor
                            id={name}
                            data={value}
                            onChange={onChange}
                            required={required}
                            aria-describedby={`${name}-error-text`}
                            config={{
                                extraPlugins: "uploadimage",
                            }}
                        />
                    </div>
                    <FormHelperText id={`${name}-error-text`}>{helperText || ""}</FormHelperText>
                </FormControl>
            </React.Fragment>
        );
    }
}

WysiwygField.propTypes = {
    name:       PropTypes.string.isRequired,
    label:      PropTypes.string.isRequired,
    onChange:   PropTypes.func.isRequired,

    value:      PropTypes.string,
    helperText: PropTypes.string,
    error:      PropTypes.bool,
    required:   PropTypes.bool,
};

export default WysiwygField;