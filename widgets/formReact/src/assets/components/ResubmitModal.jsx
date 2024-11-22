import PropTypes from 'prop-types';

const ResubmitModal = ({ setShowResubmitModal, handleSubmit }) => (
  <div className="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full" id="my-modal">
    <div className="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
      <div className="mt-3 text-center">
        <h3 className="text-lg leading-6 font-medium text-gray-900">Are you sure you want to submit again?</h3>
        <div className="mt-2 px-7 py-3">
          <p className="text-sm text-gray-500">
            A submission is already in progress. Submitting again will cancel the current submission.
          </p>
        </div>
        <div className="items-center px-4 py-3">
          <button
            id="ok-btn"
            className="px-4 py-2 bg-blue-500 text-white text-base font-medium rounded-md w-24 mr-2 hover:bg-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-300"
            onClick={() => {
              setShowResubmitModal(false);
              handleSubmit();
            }}
          >
            Yes
          </button>
          <button
            id="cancel-btn"
            className="px-4 py-2 bg-gray-300 text-black text-base font-medium rounded-md w-24 hover:bg-gray-400 focus:outline-none focus:ring-2 focus:ring-gray-300"
            onClick={() => setShowResubmitModal(false)}
          >
            No
          </button>
        </div>
      </div>
    </div>
  </div>
);

ResubmitModal.propTypes = {
  setShowResubmitModal: PropTypes.func.isRequired,
  handleSubmit: PropTypes.func.isRequired,
};

export default ResubmitModal;