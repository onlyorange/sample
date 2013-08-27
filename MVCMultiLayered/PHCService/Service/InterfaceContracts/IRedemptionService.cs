using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using PHCDto.DTO;

namespace PHCService.Service.InterfaceContracts
{
    public interface IRedemptionService
    {
        string ComputeSignature(string queryString, string key);
        string CreateQueryString(string facebookId, string pin, string callback);

        WallpaperDTO GetPrizeByPin(string pin);
        WallpaperDTO GetPrize(int prizeId);
        
        bool ConfirmPrize(int userId, int prizeId);
        void AssignPrizeToUser(int userId, int prizeId);

        RewardsDTO GetReward(int p, int id);
        bool IsEligableForReward(int userId, int prizeId);
    }
}
