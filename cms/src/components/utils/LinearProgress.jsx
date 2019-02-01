import React from "react";
import LinearProgress from "@material-ui/core/LinearProgress";

function CustomLinearProgress(props) {
    const { show } = props;

    return (
        <div style={{minHeight: 4}}>
            {show && <LinearProgress/>}
        </div>
    );
}

export default CustomLinearProgress;