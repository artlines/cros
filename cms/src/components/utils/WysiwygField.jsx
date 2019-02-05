import React from "react";
import PropTypes from "prop-types";
import {
    FormControl,
    FormHelperText,
    InputLabel,
} from "@material-ui/core";
import CKEditor from "@ckeditor/ckeditor5-react";
import ClassicEditor from "@ckeditor/ckeditor5-build-classic";
import UploadAdapter from "../../libs/ckeditor-upload-adapter";

class WysiwygField extends React.PureComponent {
    componentDidMount() {
        console.log(ClassicEditor.builtinPlugins.map(plugin => plugin.pluginName));
    }

    render() {
        const { name, label, helperText, onChange, value, error, required } = this.props;

        return (
            <React.Fragment>
                <FormControl error={error} required={required}>
                    <InputLabel htmlFor={name}>{label}</InputLabel>
                    <div style={{ marginTop: 48 }}>
                        <CKEditor
                            editor={ ClassicEditor }
                            data={value}
                            onInit={ editor => {
                                editor.plugins.get("FileRepository").createUploadAdapter = loader => {
                                    return new UploadAdapter(loader);
                                };
                            } }
                            onChange={onChange}
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