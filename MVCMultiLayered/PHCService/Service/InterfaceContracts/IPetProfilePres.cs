using System;
using System.Collections.Generic;
using System.Linq;
using System.Text;
using System.Threading.Tasks;
using PHCDto.DTO;

namespace PHCService.Service.InterfaceContracts
{
    public interface IPetProfilePres
    {
        PetProfileDTO SavePetProf(PetProfileDTO dto);
        PetProfileDTO GetPetDetails(int PID);

        int Update_petProfile(ProfileDTO pdto);

        ProfileDTO GetLastPetReg(int uid);

        int SaveFirstBadge(int uid,int pid);

        List<ChallengeDTO> ListChallenge();

        ChallengeDTO GetChallenginfo(int id);

        List<ChallengeDTO> GetPetnames(int uid,int cid);

        void SaveChallengeBadge(ChallengeDTO dt);

        ChallengeDTO Chkpetbadge(int pid,int cid);

        ChallengeDTO GetBadges(int uid);

        ProfileDTO GetBadgeCount(int uid);

        ChallengeDTO CompleteCh(int ebid);



        int chkuser(int uid);

        PetProfileDTO SaveFirstPet(ProfileDTO dto);

        void UpdateFBStatus(int ebid);

        ProfileDTO GetRewards(int uid);

        ChallengeDTO chkchallinfo(int petid, int chid);

        ChallengeDTO PreviousChallenges();


        string Delete_pet(int id);

        int savepetprofile(ProfileDTO pdto);
        ProfileDTO geteditpetprofile(int id);


        ChallengeDTO petCompleteCh(int ebid);

        ChallengeDTO InCompleteCh(int bid);

        ChallengeDTO CHNotify(int uid);

        int ChUserCnt(int cid, int uid);

        ChallengeDTO CHDExists(int chid);

        void PinUpdate(int ebid);

        void TweetUpdate(int eid);

        RewardsDTO GetWallPaperList();

        string UpdateRewards(int uid,int wid);

        bool UserHasBadge(int uid, int bid);

        ChallengeDTO GetEarnedBadgeByChallenge(int challengeId, int userId);

        bool AssignBadge(int userId, int badgeId, int? petId);

        bool IsContestChallenge(int challengeId);
        int GetRewardIdFromChallengeId(int challengeId);
        string GetRewardNameFromChallengeId(int challengeId);
    }
}
