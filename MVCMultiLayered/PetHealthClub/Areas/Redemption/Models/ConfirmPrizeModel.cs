using System;
using System.Collections.Generic;
using System.Linq;
using System.Web;

namespace PetHealthClub.Areas.Redemption.Models
{
    /// <summary>
    /// Model for confirming a token was issued their prize
    /// </summary>
    public class ConfirmPrizeModel
    {
        /// <summary>
        /// facebook_id
        /// </summary>
        public string f { get; set; }
        /// <summary>
        /// pin
        /// </summary>
        public string p { get; set; }
        /// <summary>
        /// signature
        /// </summary>
        public string s { get; set; }
    }

    /// <summary>
    /// Returns a response to the validation request
    /// </summary>
    public class ConfirmPrizeResponseModel
    {
        public string Status { get; set; }
        public string Message { get; set; }
    }
}